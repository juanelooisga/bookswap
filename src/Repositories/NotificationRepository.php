<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

final class NotificationRepository extends BaseRepository
{
    public function create(int $usuarioId, string $tipo, string $mensaje): int
    {
        $sql = 'INSERT INTO notificaciones (usuario_id, tipo, mensaje, leida)
                VALUES (:usuario_id, :tipo, :mensaje, 0)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':tipo' => $tipo,
            ':mensaje' => $mensaje,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(int $usuarioId): array
    {
        $sql = 'SELECT id, usuario_id, tipo, mensaje, leida, creado_en
                FROM notificaciones
                WHERE usuario_id = :usuario_id
                ORDER BY id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
