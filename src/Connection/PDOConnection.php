<?php

namespace App\Connection;

use App\Interfaces\ConnectionInterface;
use PDO;

/**
 * Class PDOConnection
 * @package App\Connection
 * @codeCoverageIgnore
 */
class PDOConnection implements ConnectionInterface
{
    /**
     * @return PDO
     * @codeCoverageIgnore
     */
    public function getConnection(): PDO
    {
        return new PDO($_ENV['DB_CONFIG'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }
}
