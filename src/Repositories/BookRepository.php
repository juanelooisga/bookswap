<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use Juanc\Bookswap\Models\Book;
use PDO;

final class BookRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO libros (usuario_id, titulo, autor, genero, descripcion_estado, ubicacion, disponible)
                VALUES (:usuario_id, :titulo, :autor, :genero, :descripcion_estado, :ubicacion, 1)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $data['usuario_id'],
            ':titulo' => $data['titulo'],
            ':autor' => $data['autor'],
            ':genero' => $data['genero'] ?? null,
            ':descripcion_estado' => $data['descripcion_estado'] ?? null,
            ':ubicacion' => $data['ubicacion'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allAvailable(): array
    {
        $sql = 'SELECT id, usuario_id, titulo, autor, genero, descripcion_estado, ubicacion, disponible, creado_en
                FROM libros
                WHERE disponible = 1
                ORDER BY id DESC';

        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapRowsToArrays($rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, usuario_id, titulo, autor, genero, descripcion_estado, ubicacion, disponible, creado_en
                FROM libros
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        return Book::fromArray($row)->toArray();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchAvailable(string $query): array
    {
        $sql = 'SELECT id, usuario_id, titulo, autor, genero, descripcion_estado, ubicacion, disponible, creado_en
                FROM libros
                WHERE disponible = 1
                  AND (
                    COALESCE(titulo, \'\') LIKE :q_titulo
                    OR COALESCE(autor, \'\') LIKE :q_autor
                    OR COALESCE(genero, \'\') LIKE :q_genero
                    OR COALESCE(ubicacion, \'\') LIKE :q_ubicacion
                  )
                ORDER BY id DESC';

        $stmt = $this->db->prepare($sql);
        $like = '%' . $query . '%';
        $stmt->execute([
            ':q_titulo' => $like,
            ':q_autor' => $like,
            ':q_genero' => $like,
            ':q_ubicacion' => $like,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markUnavailable(int $id): bool
    {
        $sql = 'UPDATE libros
                SET disponible = 0
                WHERE id = :id AND disponible = 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function userExists(int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function mapRowsToArrays(array $rows): array
    {
        $books = [];
        foreach ($rows as $row) {
            $books[] = Book::fromArray($row)->toArray();
        }

        return $books;
    }
}

