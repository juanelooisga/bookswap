<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Config\Database;
use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Utils\Env;

final class HealthController extends BaseController
{
    public function index(Request $request): void
    {
        // En entorno web valida conexión real; si falla, se devolverá JSON de error (500).
        if (PHP_SAPI !== 'cli') {
            Database::connection()->query('SELECT 1');
        }

        $this->json([
            'service' => 'BookSwap',
            'status' => 'ok',
            'database' => 'connected',
        ]);
    }
}
