<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Models;

/**
 * Clase base para entidades de dominio.
 * Las propiedades concretas se añadirán por modelo (User, Book, etc.).
 */
abstract class Model
{
    public function __construct(
        protected ?int $id = null,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
