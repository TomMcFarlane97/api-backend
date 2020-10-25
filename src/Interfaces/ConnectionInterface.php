<?php

namespace App\Interfaces;

use PDO;

interface ConnectionInterface
{
    public function getConnection(): PDO;
}
