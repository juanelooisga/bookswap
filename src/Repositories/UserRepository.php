<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use PDO;

final class UserRepository extends BaseRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO usuarios (nombre, correo, telefono, ubicacion, preferencias_literarias, password)
                VALUES (:nombre, :correo, :telefono, :ubicacion, :preferencias_literarias, :password)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':correo' => $data['correo'],
            ':telefono' => $data['telefono'] ?? null,
            ':ubicacion' => $data['ubicacion'] ?? null,
            ':preferencias_literarias' => $data['preferencias_literarias'] ?? null,
            ':password' => $data['password'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $sql = 'SELECT id, nombre, correo, telefono, ubicacion, preferencias_literarias, creado_en
                FROM usuarios
                ORDER BY id DESC';

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, nombre, correo, telefono, ubicacion, preferencias_literarias, creado_en
                FROM usuarios
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user === false ? null : $user;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => $email]);

        return $stmt->fetchColumn() !== false;
    }
}

