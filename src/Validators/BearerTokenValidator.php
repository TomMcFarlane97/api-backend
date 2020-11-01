<?php

namespace App\Validators;

use App\Exceptions\ImANumptyException;
use App\Helpers\TokenPayload;
use Firebase\JWT\JWT;

class BearerTokenValidator
{
    /** @var array<string, string|int> */
    private array $token;

    /**
     * BearerTokenValidator constructor.
     * @param string $token
     * @throws ImANumptyException
     */
    public function __construct(string $token)
    {
        $this->token = $this->validateToken($token);
    }

    /**
     * @param string $token
     * @return array<string, string|int>
     * @throws ImANumptyException
     */
    private function validateToken(string $token): array
    {
        return (array) JWT::decode($token, TokenPayload::getPrivateKey(), [TokenPayload::getEncodingMethod()]);
    }

    /**
     * @return array<string, string|int>
     */
    public function getToken(): array
    {
        return $this->token;
    }
}
