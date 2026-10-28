<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Repositories;

use Juanc\Bookswap\Config\Database;
use PDO;

abstract class BaseRepository
{
    protected PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? Database::connection();
    }
}
