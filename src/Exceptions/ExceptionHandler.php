<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Exceptions;

use Juanc\Bookswap\Utils\Env;
use Juanc\Bookswap\Utils\JsonResponse;
use Throwable;

final class ExceptionHandler
{
    public function register(): void
    {
        set_exception_handler([$this, 'render']);
        set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
    }

    public function render(Throwable $e): void
    {
        $debug = Env::bool('APP_DEBUG', false);

        if ($e instanceof HttpException) {
            JsonResponse::error($e->getMessage(), $e->statusCode());
        }

        $status = 500;
        $message = $debug ? $e->getMessage() : 'Error interno del servidor.';

        $payload = ['success' => false, 'message' => $message];
        if ($debug) {
            $payload['debug'] = [
                'type' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }

        JsonResponse::send($payload, $status);
    }
}
