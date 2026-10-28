<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use Juanc\Bookswap\Models\Rating;
use PDO;

final class RatingRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO calificaciones
                (usuario_calificado_id, usuario_calificador_id, solicitud_id, puntuacion, comentario)
                VALUES (:calificado, :calificador, :solicitud, :puntuacion, :comentario)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':calificado' => $data['usuario_calificado_id'],
            ':calificador' => $data['usuario_calificador_id'],
            ':solicitud' => $data['solicitud_id'],
            ':puntuacion' => $data['puntuacion'],
            ':comentario' => $data['comentario'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUsuarioCalificado(int $usuarioId): array
    {
        $sql = 'SELECT id, usuario_calificado_id, usuario_calificador_id, solicitud_id, puntuacion, comentario, creado_en
                FROM calificaciones
                WHERE usuario_calificado_id = :usuario_id
                ORDER BY id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapRowsToArrays($rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, usuario_calificado_id, usuario_calificador_id, solicitud_id, puntuacion, comentario, creado_en
                FROM calificaciones
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Rating::fromArray($row)->toArray();
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function mapRowsToArrays(array $rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = Rating::fromArray($row)->toArray();
        }

        return $items;
    }
}
