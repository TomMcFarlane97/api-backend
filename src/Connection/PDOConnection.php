<?php

namespace App\Connection;

use App\Interfaces\ConnectionInterface;
use PDO;

class PDOConnection implements ConnectionInterface
{
    public function getConnection(): PDO
    {
        return new PDO($_ENV['DB_CONFIG'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }
}
