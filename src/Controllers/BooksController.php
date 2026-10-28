<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\BookRepository;
use Throwable;

final class BooksController extends BaseController
{
    private BookRepository $books;

    public function __construct()
    {
        $this->books = new BookRepository();
    }

    public function store(Request $request): void
    {
        $input = $this->getInput($request);

        $usuarioId = trim((string) ($input['usuario_id'] ?? ''));
        $titulo = trim((string) ($input['titulo'] ?? ''));
        $autor = trim((string) ($input['autor'] ?? ''));

        $errors = [];

        if ($usuarioId === '' || !ctype_digit($usuarioId)) {
            $errors['usuario_id'] = 'El usuario_id es obligatorio y numérico.';
        }

        if ($titulo === '') {
            $errors['titulo'] = 'El título es obligatorio.';
        }

        if ($autor === '') {
            $errors['autor'] = 'El autor es obligatorio.';
        }

        if ($errors !== []) {
            $this->fail('Datos inválidos.', 422, $errors);
            return;
        }

        $userIdInt = (int) $usuarioId;

        if (!$this->books->userExists($userIdInt)) {
            $this->fail('El usuario indicado no existe.', 404);
            return;
        }

        try {
            $id = $this->books->create([
                'usuario_id' => $userIdInt,
                'titulo' => $titulo,
                'autor' => $autor,
                'genero' => $input['genero'] ?? null,
                'descripcion_estado' => $input['descripcion_estado'] ?? null,
                'ubicacion' => $input['ubicacion'] ?? null,
            ]);
        } catch (Throwable $e) {
            $this->fail('No se pudo registrar el libro.', 500, [
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        $book = $this->books->findById($id);

        if ($book === null) {
            $this->fail('Libro creado pero no encontrado.', 500);
            return;
        }

        $this->json($book, 201);
    }

    public function index(Request $request): void
    {
        $this->json([
            'books' => $this->books->allAvailable(),
        ]);
    }

    public function show(Request $request): void
    {
        $idRaw = $request->params['id'] ?? '';

        if (!ctype_digit((string) $idRaw)) {
            $this->fail('ID de libro inválido.', 422);
            return;
        }

        $book = $this->books->findById((int) $idRaw);

        if ($book === null) {
            $this->fail('Libro no encontrado.', 404);
            return;
        }

        $this->json($book);
    }

    public function search(Request $request): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));

        if ($q === '') {
            $q = trim((string) ($request->query['q'] ?? ''));
        }

        if ($q === '') {
            $fallbackQuery = [];
            parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $fallbackQuery);
            $q = trim((string) ($fallbackQuery['q'] ?? ''));
        }

        if ($q === '') {
            $this->fail('El parámetro q es obligatorio.', 422);
            return;
        }

        try {
            $results = $this->books->searchAvailable($q);
        } catch (Throwable $e) {
            $this->fail('No se pudo realizar la búsqueda de libros.', 500, [
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        $this->json([
            'books' => $results,
        ]);
    }

    public function destroy(Request $request): void
    {
        $idRaw = $request->params['id'] ?? '';

        if (!ctype_digit((string) $idRaw)) {
            $this->fail('ID de libro inválido.', 422);
            return;
        }

        $book = $this->books->findById((int) $idRaw);

        if ($book === null) {
            $this->fail('Libro no encontrado.', 404);
            return;
        }

        if ((int) ($book['disponible'] ?? 0) === 0) {
            $this->fail('El libro ya está marcado como no disponible.', 409);
            return;
        }

        $updated = $this->books->markUnavailable((int) $idRaw);

        if (!$updated) {
            $this->fail('No se pudo actualizar el libro.', 500);
            return;
        }

        $this->json([
            'message' => 'Libro marcado como no disponible.',
            'book_id' => (int) $idRaw,
            'disponible' => 0,
        ]);
    }

    /**
     * Lee datos enviados por JSON.
     *
     * @return array<string, mixed>
     */
    protected function getInput(Request $request): array
    {
        if ($request->body !== []) {
            return $request->body;
        }

        $raw = file_get_contents('php://input');

        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
