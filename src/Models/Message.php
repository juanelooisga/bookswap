<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Models;

final class Message extends Model
{
    private int $solicitudId;
    private int $usuarioEmisorId;
    private int $usuarioReceptorId;
    private string $mensaje;
    private ?string $creadoEn;

    public function __construct(
        ?int $id,
        int $solicitudId,
        int $usuarioEmisorId,
        int $usuarioReceptorId,
        string $mensaje,
        ?string $creadoEn = null
    ) {
        parent::__construct($id);
        $this->solicitudId = $solicitudId;
        $this->usuarioEmisorId = $usuarioEmisorId;
        $this->usuarioReceptorId = $usuarioReceptorId;
        $this->mensaje = $mensaje;
        $this->creadoEn = $creadoEn;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) ($data['solicitud_id'] ?? 0),
            (int) ($data['usuario_emisor_id'] ?? 0),
            (int) ($data['usuario_receptor_id'] ?? 0),
            (string) ($data['mensaje'] ?? ''),
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
            'solicitud_id' => $this->solicitudId,
            'usuario_emisor_id' => $this->usuarioEmisorId,
            'usuario_receptor_id' => $this->usuarioReceptorId,
            'mensaje' => $this->mensaje,
            'creado_en' => $this->creadoEn,
        ];
    }
}
