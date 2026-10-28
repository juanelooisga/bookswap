<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Utils;

final class Env
{
    private static bool $loaded = false;

    public static function load(string $root): void
    {
        if (self::$loaded) {
            return;
        }

        $path = $root . '/.env';
        if (!is_readable($path)) {
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            self::$loaded = true;
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            $value = trim($value, "\"'");

            // Las variables del alojamiento tienen prioridad sobre el archivo local.
            if (getenv($key) !== false || array_key_exists($key, $_ENV)) {
                continue;
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV)) {
            return (string) $_ENV[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return (string) $value;
        }

        return $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
