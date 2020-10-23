<?php

namespace App\Factory;

use App\Connection\PDOConnection;
use App\Interfaces\ConnectionInterface;

class DatabaseFactory
{
    private static ?ConnectionInterface $connection = null;

    public static function getDatabase(): ConnectionInterface
    {
        if (!self::$connection instanceof ConnectionInterface) {
            self::$connection = (new PDOConnection());
        }
        return self::$connection;
    }
}