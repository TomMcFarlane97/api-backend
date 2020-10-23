<?php

namespace App\Factory;

use App\Connection\PDOConnection;
use App\Interfaces\ConnectionInterface;

class DatabaseFactory
{
    private static ConnectionInterface $connection;

    public static function getDatabase(): ConnectionInterface
    {
        if (!self::$connection) {
            self::$connection = (new PDOConnection());
        }
        return self::$connection;
    }
}