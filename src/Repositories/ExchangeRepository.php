<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use Juanc\Bookswap\Models\Exchange;
use PDO;

final class ExchangeRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO solicitudes_intercambio
                (usuario_solicitante_id, usuario_receptor_id, libro_solicitado_id, estado, mensaje)
                VALUES (:solicitante, :receptor, :libro, :estado, :mensaje)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':solicitante' => $data['usuario_solicitante_id'],
            ':receptor' => $data['usuario_receptor_id'],
            ':libro' => $data['libro_solicitado_id'],
            ':estado' => 'pendiente',
            ':mensaje' => $data['mensaje'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $sql = 'SELECT id, usuario_solicitante_id, usuario_receptor_id, libro_solicitado_id, estado, mensaje, creado_en
                FROM solicitudes_intercambio
                ORDER BY id DESC';

        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapRowsToArrays($rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, usuario_solicitante_id, usuario_receptor_id, libro_solicitado_id, estado, mensaje, creado_en
                FROM solicitudes_intercambio
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Exchange::fromArray($row)->toArray();
    }

    public function updateStatus(int $id, string $estado): bool
    {
        $sql = 'UPDATE solicitudes_intercambio SET estado = :estado WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':estado' => $estado, ':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function mapRowsToArrays(array $rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = Exchange::fromArray($row)->toArray();
        }

        return $items;
    }
}
