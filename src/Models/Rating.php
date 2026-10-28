<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Models;

final class Rating extends Model
{
    private int $usuarioCalificadoId;
    private int $usuarioCalificadorId;
    private int $solicitudId;
    private int $puntuacion;
    private ?string $comentario;
    private ?string $creadoEn;

    public function __construct(
        ?int $id,
        int $usuarioCalificadoId,
        int $usuarioCalificadorId,
        int $solicitudId,
        int $puntuacion,
        ?string $comentario = null,
        ?string $creadoEn = null
    ) {
        parent::__construct($id);
        $this->usuarioCalificadoId = $usuarioCalificadoId;
        $this->usuarioCalificadorId = $usuarioCalificadorId;
        $this->solicitudId = $solicitudId;
        $this->puntuacion = $puntuacion;
        $this->comentario = $comentario;
        $this->creadoEn = $creadoEn;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) ($data['usuario_calificado_id'] ?? 0),
            (int) ($data['usuario_calificador_id'] ?? 0),
            (int) ($data['solicitud_id'] ?? 0),
            (int) ($data['puntuacion'] ?? 0),
            isset($data['comentario']) ? (string) $data['comentario'] : null,
            isset($data['creado_en']) ? (string) $data['creado_en'] : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'usuario_calificado_id' => $this->usuarioCalificadoId,
            'usuario_calificador_id' => $this->usuarioCalificadorId,
            'solicitud_id' => $this->solicitudId,
            'puntuacion' => $this->puntuacion,
            'comentario' => $this->comentario,
            'creado_en' => $this->creadoEn,
        ];
    }
}
