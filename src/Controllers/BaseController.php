<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Utils\JsonResponse;

abstract class BaseController
{
    /**
     * @param array<string, mixed> $data
     */
    protected function json(array $data, int $status = 200): void
    {
        JsonResponse::send(['success' => true, 'data' => $data], $status);
    }

    /**
     * @param array<string, mixed>|list<mixed> $errors
     */
    protected function fail(string $message, int $status = 400, array $errors = []): void
    {
        if (!\Juanc\Bookswap\Utils\Env::bool('APP_DEBUG', false)) {
            unset($errors['exception']);
        }
        JsonResponse::error($message, $status, $errors);
    }

    protected function requireMethod(Request $request, string $method): void
    {
        if ($request->method !== strtoupper($method)) {
            $this->fail('Método no permitido.', 405);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getInput(Request $request): array
    {
        if ($request->body !== []) {
            return $request->body;
        }

        $raw = Request::rawBody();
        if (trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function requireId(Request $request, string $key = 'id'): int
    {
        $idRaw = $request->params[$key] ?? '';
        if (!ctype_digit((string) $idRaw)) {
            $this->fail('ID inválido.', 422);
        }

        return (int) $idRaw;
    }
}
