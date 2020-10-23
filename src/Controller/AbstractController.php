<?php

namespace App\Controller;

use App\Exceptions\ImANumptyException;
use App\Exceptions\RequestException;
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
    private const HEADER_CONTENT_TYPE = 'Content-type';
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
     * @param string[] $message
     * @return string
     * @throws ImANumptyException
     */
    protected static function jsonEncodeArray(array $message): string
    {
        $message = json_encode($message);
        if ($message === false) {
            throw new ImANumptyException('Can you even code bro', self::TEA_POT);
        }
        return $message;
    }
}