<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Models;

final class Book extends Model
{
    private int $usuarioId;
    private string $titulo;
    private string $autor;
    private ?string $genero;
    private ?string $descripcionEstado;
    private ?string $ubicacion;
    private int $disponible;
    private ?string $creadoEn;

    public function __construct(
        ?int $id,
        int $usuarioId,
        string $titulo,
        string $autor,
        ?string $genero = null,
        ?string $descripcionEstado = null,
        ?string $ubicacion = null,
        int $disponible = 1,
        ?string $creadoEn = null
    ) {
        parent::__construct($id);
        $this->usuarioId = $usuarioId;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->genero = $genero;
        $this->descripcionEstado = $descripcionEstado;
        $this->ubicacion = $ubicacion;
        $this->disponible = $disponible;
        $this->creadoEn = $creadoEn;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) ($data['usuario_id'] ?? 0),
            (string) ($data['titulo'] ?? ''),
            (string) ($data['autor'] ?? ''),
            isset($data['genero']) ? (string) $data['genero'] : null,
            isset($data['descripcion_estado']) ? (string) $data['descripcion_estado'] : null,
            isset($data['ubicacion']) ? (string) $data['ubicacion'] : null,
            isset($data['disponible']) ? (int) $data['disponible'] : 1,
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
            'usuario_id' => $this->usuarioId,
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'genero' => $this->genero,
            'descripcion_estado' => $this->descripcionEstado,
            'ubicacion' => $this->ubicacion,
            'disponible' => $this->disponible,
            'creado_en' => $this->creadoEn,
        ];
    }
}

