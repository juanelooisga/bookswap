<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\NotificationRepository;
use Juanc\Bookswap\Repositories\RatingRepository;
use Juanc\Bookswap\Repositories\UserRepository;
use Throwable;

final class UsersController extends BaseController
{
    private UserRepository $users;
    private NotificationRepository $notifications;
    private RatingRepository $ratings;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->notifications = new NotificationRepository();
        $this->ratings = new RatingRepository();
    }

    public function store(Request $request): void
    {
        $input = $request->body;

        // Fallback para clientes (como PowerShell) donde el body puede llegar vacío.
        if ($input === []) {
            $raw = file_get_contents('php://input');
            if (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $input = $decoded;
                }
            }
        }

        $nombre = trim((string) ($input['nombre'] ?? ''));
        $correo = trim((string) ($input['correo'] ?? ''));
        $password = trim((string) ($input['password'] ?? ''));

        $errors = [];
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if ($correo === '') {
            $errors['correo'] = 'El correo es obligatorio.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo no es válido.';
        }
        if ($password === '') {
            $errors['password'] = 'La contraseña es obligatoria.';
        }

        if ($errors !== []) {
            $this->fail('Datos inválidos.', 422, $errors);
        }

        if ($this->users->existsByEmail($correo)) {
            $this->fail('El correo ya está registrado.', 409);
        }

        try {
            $id = $this->users->create([
                'nombre' => $nombre,
                'correo' => $correo,
                'telefono' => $input['telefono'] ?? null,
                'ubicacion' => $input['ubicacion'] ?? null,
                'preferencias_literarias' => $input['preferencias_literarias'] ?? null,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        } catch (Throwable $e) {
            $this->fail('No se pudo registrar el usuario.', 500);
            return;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            $this->fail('Usuario creado pero no encontrado.', 500);
        }

        $this->json($user, 201);
    }

    public function index(Request $request): void
    {
        $this->json(['users' => $this->users->all()]);
    }

    public function show(Request $request): void
    {
        $idRaw = $request->params['id'] ?? '';
        if (!ctype_digit((string) $idRaw)) {
            $this->fail('ID de usuario inválido.', 422);
        }

        $user = $this->users->findById((int) $idRaw);
        if ($user === null) {
            $this->fail('Usuario no encontrado.', 404);
        }

        $this->json($user);
    }

    public function notifications(Request $request): void
    {
        $id = $this->requireId($request);
        if ($this->users->findById($id) === null) {
            $this->fail('Usuario no encontrado.', 404);
        }

        $this->json(['notifications' => $this->notifications->findByUserId($id)]);
    }

    public function ratings(Request $request): void
    {
        $id = $this->requireId($request);
        if ($this->users->findById($id) === null) {
            $this->fail('Usuario no encontrado.', 404);
        }

        $this->json(['ratings' => $this->ratings->findByUsuarioCalificado($id)]);
    }
}

