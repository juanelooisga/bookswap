<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use PDO;

final class HistoryRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO historial_intercambios
                (solicitud_id, usuario_1_id, usuario_2_id, descripcion, estado_final)
                VALUES (:solicitud_id, :usuario_1, :usuario_2, :descripcion, :estado_final)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':solicitud_id' => $data['solicitud_id'],
            ':usuario_1' => $data['usuario_1_id'],
            ':usuario_2' => $data['usuario_2_id'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':estado_final' => $data['estado_final'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $sql = 'SELECT id, solicitud_id, usuario_1_id, usuario_2_id, descripcion, estado_final, creado_en
                FROM historial_intercambios
                ORDER BY id DESC';

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
