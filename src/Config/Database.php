<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Config;

use Juanc\Bookswap\Utils\Env;
use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $name = Env::get('DB_NAME', 'bookswap');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASS', '');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $production = Env::get('APP_ENV', 'local') === 'production';
        $tls = Env::bool('DB_SSL', $production);
        if ($production && !$tls) {
            throw new RuntimeException('La conexión MySQL de producción requiere TLS.');
        }
        if ($tls) {
            $ca = Env::get('DB_SSL_CA', '/etc/secrets/mysql-ca.pem');
            if (!$ca || !is_readable($ca)) {
                throw new RuntimeException('Falta el certificado CA para MySQL.');
            }
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        try {
            self::$pdo = new PDO($dsn, $user, $pass ?? '', $options);
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo conectar a la base de datos.', 0, $e);
        }

        return self::$pdo;
    }
}
