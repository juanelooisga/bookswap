<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\BookRepository;
use Juanc\Bookswap\Repositories\ExchangeRepository;
use Juanc\Bookswap\Repositories\HistoryRepository;
use Juanc\Bookswap\Repositories\MessageRepository;
use Juanc\Bookswap\Repositories\NotificationRepository;
use Juanc\Bookswap\Repositories\UserRepository;
use Throwable;

final class ExchangesController extends BaseController
{
    private ExchangeRepository $exchanges;
    private UserRepository $users;
    private BookRepository $books;
    private NotificationRepository $notifications;
    private HistoryRepository $history;
    private MessageRepository $messages;

    public function __construct()
    {
        $this->exchanges = new ExchangeRepository();
        $this->users = new UserRepository();
        $this->books = new BookRepository();
        $this->notifications = new NotificationRepository();
        $this->history = new HistoryRepository();
        $this->messages = new MessageRepository();
    }

    public function store(Request $request): void
    {
        $input = $this->readJsonInput($request);

        $solicitante = trim((string) ($input['usuario_solicitante_id'] ?? ''));
        $receptor = trim((string) ($input['usuario_receptor_id'] ?? ''));
        $libro = trim((string) ($input['libro_solicitado_id'] ?? ''));
        $mensaje = trim((string) ($input['mensaje'] ?? ''));

        $errors = [];

        if ($solicitante === '' || !ctype_digit($solicitante)) {
            $errors['usuario_solicitante_id'] = 'Obligatorio y numérico.';
        }

        if ($receptor === '' || !ctype_digit($receptor)) {
            $errors['usuario_receptor_id'] = 'Obligatorio y numérico.';
        }

        if ($libro === '' || !ctype_digit($libro)) {
            $errors['libro_solicitado_id'] = 'Obligatorio y numérico.';
        }

        if ($mensaje === '') {
            $errors['mensaje'] = 'El mensaje es obligatorio.';
        }

        if ($errors !== []) {
            $this->fail('Datos inválidos.', 422, $errors);
            return;
        }

        $solicitanteId = (int) $solicitante;
        $receptorId = (int) $receptor;
        $libroId = (int) $libro;

        if ($solicitanteId === $receptorId) {
            $this->fail('El solicitante y el receptor no pueden ser el mismo usuario.', 422);
            return;
        }

        if ($this->users->findById($solicitanteId) === null) {
            $this->fail('Usuario solicitante no encontrado.', 404);
            return;
        }

        if ($this->users->findById($receptorId) === null) {
            $this->fail('Usuario receptor no encontrado.', 404);
            return;
        }

        $book = $this->books->findById($libroId);

        if ($book === null) {
            $this->fail('Libro no encontrado.', 404);
            return;
        }

        if ((int) ($book['disponible'] ?? 0) !== 1) {
            $this->fail('El libro solicitado no está disponible para intercambio.', 409);
            return;
        }

        if ((int) $book['usuario_id'] !== $receptorId) {
            $this->fail('El receptor debe ser el propietario del libro.', 422);
            return;
        }

        try {
            $id = $this->exchanges->create([
                'usuario_solicitante_id' => $solicitanteId,
                'usuario_receptor_id' => $receptorId,
                'libro_solicitado_id' => $libroId,
                'mensaje' => $mensaje,
            ]);

            $this->notifications->create(
                $receptorId,
                'solicitud_intercambio',
                'Has recibido una nueva solicitud de intercambio.'
            );
        } catch (Throwable $e) {
            $this->fail('No se pudo crear la solicitud de intercambio.', 500, [
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        $exchange = $this->exchanges->findById($id);

        if ($exchange === null) {
            $this->fail('Solicitud creada pero no encontrada.', 500);
            return;
        }

        $this->json($exchange, 201);
    }

    public function index(Request $request): void
    {
        $this->json([
            'exchanges' => $this->exchanges->all(),
        ]);
    }

    public function accept(Request $request): void
    {
        $id = $this->requireId($request);

        $exchange = $this->exchanges->findById($id);

        if ($exchange === null) {
            $this->fail('Solicitud no encontrada.', 404);
            return;
        }

        if (($exchange['estado'] ?? '') !== 'pendiente') {
            $this->fail('Solo se pueden aceptar solicitudes pendientes.', 409);
            return;
        }

        $db = \Juanc\Bookswap\App::database();
        $db->beginTransaction();
        try {
            if (!$this->exchanges->updateStatus($id, 'aceptada')) {
                $db->rollBack();
                $this->fail('La solicitud ya cambió de estado.', 409);
                return;
            }

            $solicitanteId = (int) $exchange['usuario_solicitante_id'];
            $receptorId = (int) $exchange['usuario_receptor_id'];

            $this->notifications->create(
                $solicitanteId,
                'solicitud_aceptada',
                'Tu solicitud de intercambio fue aceptada.'
            );

            $this->history->create([
                'solicitud_id' => $id,
                'usuario_1_id' => $solicitanteId,
                'usuario_2_id' => $receptorId,
                'descripcion' => 'Intercambio aceptado',
                'estado_final' => 'exitoso',
            ]);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            $this->fail('No se pudo aceptar la solicitud.', 500, [
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        $updated = $this->exchanges->findById($id);

        $this->json($updated ?? [
            'id' => $id,
            'estado' => 'aceptada',
        ]);
    }

    public function reject(Request $request): void
    {
        $id = $this->requireId($request);

        $exchange = $this->exchanges->findById($id);

        if ($exchange === null) {
            $this->fail('Solicitud no encontrada.', 404);
            return;
        }

        if (($exchange['estado'] ?? '') !== 'pendiente') {
            $this->fail('Solo se pueden rechazar solicitudes pendientes.', 409);
            return;
        }

        $db = \Juanc\Bookswap\App::database();
        $db->beginTransaction();
        try {
            if (!$this->exchanges->updateStatus($id, 'rechazada')) {
                $db->rollBack();
                $this->fail('La solicitud ya cambió de estado.', 409);
                return;
            }

            $this->notifications->create(
                (int) $exchange['usuario_solicitante_id'],
                'solicitud_rechazada',
                'Tu solicitud de intercambio fue rechazada.'
            );
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            $this->fail('No se pudo rechazar la solicitud.', 500, [
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        $updated = $this->exchanges->findById($id);

        $this->json($updated ?? [
            'id' => $id,
            'estado' => 'rechazada',
        ]);
    }

    public function messages(Request $request): void
    {
        $id = $this->requireId($request);

        if ($this->exchanges->findById($id) === null) {
            $this->fail('Solicitud no encontrada.', 404);
            return;
        }

        $this->json([
            'messages' => $this->messages->findBySolicitudId($id),
        ]);
    }

    /**
     * Lee el JSON enviado desde PowerShell, Thunder Client o navegador.
     *
     * @return array<string, mixed>
     */
    private function readJsonInput(Request $request): array
    {
        if ($request->body !== []) {
            return $request->body;
        }

        $raw = file_get_contents('php://input');

        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}