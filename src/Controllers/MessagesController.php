<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\ExchangeRepository;
use Juanc\Bookswap\Repositories\MessageRepository;
use Juanc\Bookswap\Repositories\UserRepository;
use Throwable;

final class MessagesController extends BaseController
{
    private MessageRepository $messages;
    private ExchangeRepository $exchanges;
    private UserRepository $users;

    public function __construct()
    {
        $this->messages = new MessageRepository();
        $this->exchanges = new ExchangeRepository();
        $this->users = new UserRepository();
    }

    public function store(Request $request): void
    {
        $input = $this->getInput($request);

        $solicitudId = trim((string) ($input['solicitud_id'] ?? ''));
        $emisor = trim((string) ($input['usuario_emisor_id'] ?? ''));
        $receptor = trim((string) ($input['usuario_receptor_id'] ?? ''));
        $mensaje = trim((string) ($input['mensaje'] ?? ''));

        $errors = [];
        if ($solicitudId === '' || !ctype_digit($solicitudId)) {
            $errors['solicitud_id'] = 'Obligatorio y numérico.';
        }
        if ($emisor === '' || !ctype_digit($emisor)) {
            $errors['usuario_emisor_id'] = 'Obligatorio y numérico.';
        }
        if ($receptor === '' || !ctype_digit($receptor)) {
            $errors['usuario_receptor_id'] = 'Obligatorio y numérico.';
        }
        if ($mensaje === '') {
            $errors['mensaje'] = 'El mensaje es obligatorio.';
        }
        if ($errors !== []) {
            $this->fail('Datos inválidos.', 422, $errors);
        }

        $solicitudIdInt = (int) $solicitudId;
        if ($this->exchanges->findById($solicitudIdInt) === null) {
            $this->fail('Solicitud no encontrada.', 404);
        }
        if ($this->users->findById((int) $emisor) === null) {
            $this->fail('Usuario emisor no encontrado.', 404);
        }
        if ($this->users->findById((int) $receptor) === null) {
            $this->fail('Usuario receptor no encontrado.', 404);
        }

        try {
            $id = $this->messages->create([
                'solicitud_id' => $solicitudIdInt,
                'usuario_emisor_id' => (int) $emisor,
                'usuario_receptor_id' => (int) $receptor,
                'mensaje' => $mensaje,
            ]);
        } catch (Throwable $e) {
            $this->fail('No se pudo enviar el mensaje.', 500);
            return;
        }

        $rows = $this->messages->findBySolicitudId($solicitudIdInt);
        $created = null;
        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $created = $row;
                break;
            }
        }

        $this->json($created ?? ['id' => $id, 'mensaje' => $mensaje], 201);
    }
}
