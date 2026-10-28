<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Http;

use Juanc\Bookswap\Exceptions\NotFoundException;

final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    private string $prefix;

    public function __construct(string $prefix = '/api')
    {
        $this->prefix = $prefix;
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $normalized = $this->normalizePath($path);
        $this->routes[$method][$normalized] = $handler;
    }

    public function dispatch(Request $request): void
    {
        $path = $this->resolvePath($request->path);
        $methodRoutes = $this->routes[$request->method] ?? [];
        $handler = $methodRoutes[$path] ?? null;

        if ($handler !== null) {
            $handler($request);
            return;
        }

        foreach ($methodRoutes as $pattern => $candidate) {
            if (!str_contains($pattern, '{')) {
                continue;
            }

            $params = $this->matchDynamicRoute($pattern, $path);
            if ($params === null) {
                continue;
            }

            $request->params = $params;
            $candidate($request);
            return;
        }

        throw new NotFoundException("Ruta no encontrada: {$request->method} {$path}");
    }

    private function resolvePath(string $path): string
    {
        $path = rtrim($path, '/') ?: '/';
        $prefix = rtrim($this->prefix, '/') ?: '';

        if ($prefix !== '' && str_starts_with($path, $prefix)) {
            $path = substr($path, strlen($prefix)) ?: '/';
        }

        return $this->normalizePath($path);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    /**
     * @return array<string, string>|null
     */
    private function matchDynamicRoute(string $pattern, string $path): ?array
    {
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $matches): string {
                $param = $matches[1];

                // Regla simple: parámetros tipo "id" solo aceptan números.
                if (strtolower($param) === 'id' || str_ends_with(strtolower($param), '_id')) {
                    return '(?P<' . $param . '>\d+)';
                }

                return '(?P<' . $param . '>[^/]+)';
            },
            $pattern
        );
        if ($regex === null) {
            return null;
        }

        if (!preg_match('#^' . $regex . '$#', $path, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
