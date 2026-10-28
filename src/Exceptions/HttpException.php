<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Exceptions;

use Exception;

class HttpException extends Exception
{
    private int $statusCode;

    public function __construct(
        string $message,
        int $statusCode = 400,
        ?\Throwable $previous = null
    ) {
        $this->statusCode = $statusCode;
        parent::__construct($message, $statusCode, $previous);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
