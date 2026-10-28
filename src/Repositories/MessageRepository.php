<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use Juanc\Bookswap\Models\Message;
use PDO;

final class MessageRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO mensajes (solicitud_id, usuario_emisor_id, usuario_receptor_id, mensaje)
                VALUES (:solicitud_id, :emisor, :receptor, :mensaje)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':solicitud_id' => $data['solicitud_id'],
            ':emisor' => $data['usuario_emisor_id'],
            ':receptor' => $data['usuario_receptor_id'],
            ':mensaje' => $data['mensaje'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findBySolicitudId(int $solicitudId): array
    {
        $sql = 'SELECT id, solicitud_id, usuario_emisor_id, usuario_receptor_id, mensaje, creado_en
                FROM mensajes
                WHERE solicitud_id = :solicitud_id
                ORDER BY id ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':solicitud_id' => $solicitudId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapRowsToArrays($rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, solicitud_id, usuario_emisor_id, usuario_receptor_id, mensaje, creado_en
                FROM mensajes
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Message::fromArray($row)->toArray();
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function mapRowsToArrays(array $rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = Message::fromArray($row)->toArray();
        }

        return $items;
    }
}
