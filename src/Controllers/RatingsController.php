<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\ExchangeRepository;
use Juanc\Bookswap\Repositories\RatingRepository;
use Juanc\Bookswap\Repositories\UserRepository;
use Throwable;

final class RatingsController extends BaseController
{
    private RatingRepository $ratings;
    private UserRepository $users;
    private ExchangeRepository $exchanges;

    public function __construct()
    {
        $this->ratings = new RatingRepository();
        $this->users = new UserRepository();
        $this->exchanges = new ExchangeRepository();
    }

    public function store(Request $request): void
    {
        $input = $this->getInput($request);

        $calificado = trim((string) ($input['usuario_calificado_id'] ?? ''));
        $calificador = trim((string) ($input['usuario_calificador_id'] ?? ''));
        $solicitud = trim((string) ($input['solicitud_id'] ?? ''));
        $puntuacion = trim((string) ($input['puntuacion'] ?? ''));

        $errors = [];
        if ($calificado === '' || !ctype_digit($calificado)) {
            $errors['usuario_calificado_id'] = 'Obligatorio y numérico.';
        }
        if ($calificador === '' || !ctype_digit($calificador)) {
            $errors['usuario_calificador_id'] = 'Obligatorio y numérico.';
        }
        if ($solicitud === '' || !ctype_digit($solicitud)) {
            $errors['solicitud_id'] = 'Obligatorio y numérico.';
        }
        if ($puntuacion === '' || !ctype_digit($puntuacion)) {
            $errors['puntuacion'] = 'Obligatoria y numérica (1-5).';
        } elseif ((int) $puntuacion < 1 || (int) $puntuacion > 5) {
            $errors['puntuacion'] = 'Debe estar entre 1 y 5.';
        }
        if ($errors !== []) {
            $this->fail('Datos inválidos.', 422, $errors);
        }

        if ($this->users->findById((int) $calificado) === null) {
            $this->fail('Usuario calificado no encontrado.', 404);
        }
        if ($this->users->findById((int) $calificador) === null) {
            $this->fail('Usuario calificador no encontrado.', 404);
        }
        if ($this->exchanges->findById((int) $solicitud) === null) {
            $this->fail('Solicitud no encontrada.', 404);
        }

        try {
            $id = $this->ratings->create([
                'usuario_calificado_id' => (int) $calificado,
                'usuario_calificador_id' => (int) $calificador,
                'solicitud_id' => (int) $solicitud,
                'puntuacion' => (int) $puntuacion,
                'comentario' => $input['comentario'] ?? null,
            ]);
        } catch (Throwable $e) {
            $this->fail('No se pudo registrar la calificación.', 500);
            return;
        }

        $rating = $this->ratings->findById($id);
        if ($rating === null) {
            $this->fail('Calificación creada pero no encontrada.', 500);
        }

        $this->json($rating, 201);
    }
}
