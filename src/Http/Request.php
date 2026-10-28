<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Http;

final class Request
{
    private static ?string $rawBody = null;

    public string $method;
    public string $path;
    public array $query;
    public array $body;
    public array $params;

    public static function rawBody(): string
    {
        if (self::$rawBody === null) {
            self::$rawBody = file_get_contents('php://input') ?: '';
        }

        return self::$rawBody;
    }

    public function __construct(string $method, string $path, array $query, array $body, array $params = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->params = $params;
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $query = $_GET;
        $body = [];

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $raw = self::rawBody();
            if (trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $body = $decoded;
                }
            }
            if ($body === [] && $_POST !== []) {
                $body = $_POST;
            }
        }

        return new self($method, $path, $query, $body);
    }
}
