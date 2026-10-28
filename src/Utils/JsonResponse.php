<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Utils;

final class JsonResponse
{
    /** En tests se puede desactivar para no terminar el proceso. */
    public static bool $haltAfterSend = true;

    /**
     * @param array<string, mixed>|list<mixed> $data
     */
    public static function send(array $data, int $status = 200): void
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        if (self::$haltAfterSend) {
            exit;
        }
    }

    /**
     * @param array<string, mixed>|list<mixed> $errors
     */
    public static function error(
        string $message,
        int $status = 400,
        array $errors = [],
    ): void {
        $body = ['success' => false, 'message' => $message];
        if ($errors !== []) {
            $body['errors'] = $errors;
        }
        self::send($body, $status);
    }
}
