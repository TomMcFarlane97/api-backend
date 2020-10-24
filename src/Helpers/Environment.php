<?php

namespace App\Helpers;

class Environment
{
    private const LIVE = 'production';
    private const DEV = 'development';

    public static function isProduction(): bool
    {
        return $_ENV['environment'] === self::LIVE;
    }
}