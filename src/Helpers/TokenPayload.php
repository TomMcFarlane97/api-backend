<?php

namespace App\Helpers;

use App\Exceptions\ImANumptyException;
use Carbon\Carbon;

class TokenPayload
{
    private const DEFAULT_ENCODING_METHOD = 'HS256';

    /**
     * @param int $userId
     * @return string[]
     * @throws ImANumptyException
     */
    public static function toArray(int $userId): array
    {
        $time = Carbon::now();
//        var_dump($time->toString(), $time->addHour()->toString()); exit;
        $serviceAccountEmail = self::getServiceAccountEmail();
        return [
            'iss' => $serviceAccountEmail,
            'sub' => $serviceAccountEmail,
            'aud' => self::getAudience(),
            'iat' => $time->toString(),
            'exp' => $time->addHour(),
            'uid' => $userId,
        ];
    }

    /**
     * @return string
     * @throws ImANumptyException
     */
    public static function getPrivateKey(): string
    {
        if (empty($_ENV['PRIVATE_KEY'])) {
            throw new ImANumptyException(
                'Key "PRIVATE_KEY" is not configured in ENV',
                StatusCodes::NOT_IMPLEMENTED
            );
        }
        return $_ENV['PRIVATE_KEY'];
    }

    public static function getEncodingMethod(): string
    {
        return $_ENV['ENCODING_METHOD'] ?? self::DEFAULT_ENCODING_METHOD;
    }

    /**
     * @return string
     * @throws ImANumptyException
     */
    private static function getServiceAccountEmail(): string
    {
        if (empty($_ENV['SERVICE_ACCOUNT_EMAIL'])) {
            throw new ImANumptyException(
                'Key "SERVICE_ACCOUNT_EMAIL" is not configured in ENV',
                StatusCodes::NOT_IMPLEMENTED
            );
        }
        return $_ENV['SERVICE_ACCOUNT_EMAIL'];
    }

    /**
     * @return string
     * @throws ImANumptyException
     */
    private static function getAudience(): string
    {
        if (empty($_ENV['TOKEN_AUDIENCE'])) {
            throw new ImANumptyException(
                'Key "TOKEN_AUDIENCE" is not configured in ENV',
                StatusCodes::NOT_IMPLEMENTED
            );
        }
        return $_ENV['TOKEN_AUDIENCE'];
    }
}
