<?php

namespace App\Controller;

use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RequestException;
use App\Interfaces\ConvertToArrayInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractController
{
    protected const JSON = 'application/json';
    protected const ACCEPTED = 200;
    protected const BAD_REQUEST = 400;
    protected const UNSUPPORTED_MIME_TYPE = 415;
    protected const TEA_POT = 418;
    protected const UNPROCESSABLE_ENTITY = 422;
    protected const CREATED = 201;
    protected const INTERNAL_SERVER_ERROR = 500;
    protected const HEADER_CONTENT_TYPE = 'Content-type';
    private const HEADER_ACCEPT = 'Accept';

    /**
     * Validates is JSON request
     * @param RequestInterface $request
     * @throws RequestException
     */
    protected function validateRequest(RequestInterface $request): void
    {
        $contentType = $request->getHeader(self::HEADER_CONTENT_TYPE);
        if (!empty($contentType[0]) && !str_contains($contentType[0], self::JSON)
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
     * @param array<int, string[]>|ConvertToArrayInterface[]|string[] $message
     * @param bool $canConvertToArray
     * @return string
     * @throws EntityException
     * @throws ImANumptyException
     */
    protected static function jsonEncodeArray(array $message, bool $canConvertToArray = false): string
    {
        $message = $canConvertToArray ? self::convertObjectToEncodedArray($message) : $message = json_encode($message);
        if ($message === false) {
            throw new ImANumptyException('Can you even code bro', self::TEA_POT);
        }
        return $message;
    }

    /**
     * @param array<ConvertToArrayInterface|array<string>|string> $entities
     * @return string
     * @throws EntityException|ImANumptyException
     */
    private static function convertObjectToEncodedArray(array $entities): string
    {
        $message = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof ConvertToArrayInterface) {
                throw new ImANumptyException(sprintf('Can you even code bro. Method - %s', __METHOD__), self::TEA_POT);
            }
            $message[] = $entity->convertToArray();
        }

        return self::jsonEncodeArray($message);
    }
}