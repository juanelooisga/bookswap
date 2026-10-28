<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Models;

final class Exchange extends Model
{
    private int $usuarioSolicitanteId;
    private int $usuarioReceptorId;
    private int $libroSolicitadoId;
    private string $estado;
    private ?string $mensaje;
    private ?string $creadoEn;

    public function __construct(
        ?int $id,
        int $usuarioSolicitanteId,
        int $usuarioReceptorId,
        int $libroSolicitadoId,
        string $estado = 'pendiente',
        ?string $mensaje = null,
        ?string $creadoEn = null
    ) {
        parent::__construct($id);
        $this->usuarioSolicitanteId = $usuarioSolicitanteId;
        $this->usuarioReceptorId = $usuarioReceptorId;
        $this->libroSolicitadoId = $libroSolicitadoId;
        $this->estado = $estado;
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
            (int) ($data['usuario_solicitante_id'] ?? 0),
            (int) ($data['usuario_receptor_id'] ?? 0),
            (int) ($data['libro_solicitado_id'] ?? 0),
            (string) ($data['estado'] ?? 'pendiente'),
            isset($data['mensaje']) ? (string) $data['mensaje'] : null,
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
            'usuario_solicitante_id' => $this->usuarioSolicitanteId,
            'usuario_receptor_id' => $this->usuarioReceptorId,
            'libro_solicitado_id' => $this->libroSolicitadoId,
            'estado' => $this->estado,
            'mensaje' => $this->mensaje,
            'creado_en' => $this->creadoEn,
        ];
    }
}
