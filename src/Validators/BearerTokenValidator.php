<?php

namespace App\Validators;

use Firebase\JWT\JWT;

class BearerTokenValidator
{
    /** @var array<string, string|int> */
    private array $token;

    public function __construct(string $token)
    {
        $this->token = $this->setToken($token);
    }

    private function setToken(string $token): array
    {
        JWT::decode($token);
    }
}
