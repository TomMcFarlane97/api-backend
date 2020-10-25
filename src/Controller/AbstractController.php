<?php

namespace App\Controller;

use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RequestException;
use App\Helpers\Environment;
use App\Interfaces\ConvertToArrayInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

abstract class AbstractController
{
    public const ACCEPTED = 200;
    public const BAD_REQUEST = 400;
    public const UNSUPPORTED_MIME_TYPE = 415;
    public const TEA_POT = 418;
    public const UNPROCESSABLE_ENTITY = 422;
    public const CREATED = 201;
    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    protected const JSON = 'application/json';
    protected const HEADER_CONTENT_TYPE = 'Content-type';
    private const HEADER_ACCEPT = 'Accept';

    /** @var string[] */
    protected array $jsonResponseHeader = [self::HEADER_CONTENT_TYPE => self::JSON];

    /**
     * @param RequestInterface $request
     * @throws RequestException
     */
    protected function validateRequestIsJson(RequestInterface $request): void
    {
        $contentType = $request->getHeader(self::HEADER_CONTENT_TYPE);
        if (
            !empty($contentType[0]) && !str_contains($contentType[0], self::JSON)
            && !empty($request->getBody()->getContents())
        ) {
            throw new RequestException(
                sprintf('Request header "%s" must be type "%s"', self::HEADER_CONTENT_TYPE, self::JSON),
                self::UNSUPPORTED_MIME_TYPE
            );
        }

        $acceptHeader = $request->getHeader(self::HEADER_ACCEPT);
        if (!empty($acceptHeader[0]) && !str_contains($acceptHeader[0], self::JSON)) {
            throw new RequestException(
                sprintf('Request header "%s" must be type "%s"', self::HEADER_ACCEPT, self::JSON),
                self::BAD_REQUEST
            );
        }
    }

    /**
     * @param Throwable $exception
     * @return string[]
     */
    protected function getMessage(Throwable $exception): array
    {
        $message = ['message' => $exception->getMessage()];
        if (!Environment::isProduction()) {
            $message = array_merge($message, $exception->getTrace());
        }
        return $message;
    }

    /**
     * @param array<ConvertToArrayInterface|array<string>|string> $entities
     * @return array<int, array<string, mixed>>
     * @throws EntityException|ImANumptyException
     */
    protected static function convertObjectToArray(array $entities): array
    {
        $message = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof ConvertToArrayInterface) {
                throw new ImANumptyException(sprintf('Can you even code bro. Method - %s', __METHOD__), self::TEA_POT);
            }
            $message[] = $entity->convertToArray();
        }

        return $message;
    }
}
