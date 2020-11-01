<?php

namespace App\Helpers;

use App\Exceptions\ImANumptyException;
use Carbon\Carbon;

class TokenPayload
{
    public const BEARER_TOKEN = 'bearer';
    public const REFRESH_TOKEN = 'refresh';
    private const DEFAULT_ENCODING_METHOD = 'HS256';

    /**
     * @param int $userId
     * @param bool $isRefresh
     * @return array<string, float|int|string>
     * @throws ImANumptyException
     */
    public static function toArray(int $userId, bool $isRefresh = false): array
    {
        $time = Carbon::now();
        $serviceAccountEmail = self::getServiceAccountEmail();
        return [
            'iss' => $serviceAccountEmail,
            'sub' => $serviceAccountEmail,
            'aud' => self::getAudience(),
            'iat' => $time->timestamp,
            'exp' => $isRefresh ? $time->addMonth()->timestamp : $time->addHour()->timestamp,
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

    /**
     * @return string
     */
    public static function getEncodingMethod(): string
    {
        return $_ENV['ENCODING_METHOD'] ?? self::DEFAULT_ENCODING_METHOD;
    }

    /**
     * @return string[]
     */
    public static function routesToExclude(): array
    {
        return [
            RequestMethods::POST => AUTHENTICATE_ROUTE,
        ];
    }

    /**
     * @return string[]
     */
    public static function methodsToExclude(): array
    {
        return [
            RequestMethods::OPTIONS,
        ];
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
