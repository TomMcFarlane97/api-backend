<?php

namespace App\Controller;

use App\Exceptions\RequestException;
use Psr\Http\Message\RequestInterface;

abstract class AbstractController
{
    protected const JSON = 'application/json';
    protected const ACCEPTED = 200;
    protected const BAD_REQUEST = 400;
    protected const CREATED = 201;
    protected const INTERNAL_SERVER_ERROR = 500;

    /**
     * Validates is JSON request
     * @param RequestInterface $request
     * @throws RequestException
     */
    protected function validateRequest(RequestInterface $request): void
    {
        $contentType = $request->getHeader('Content-type');
        if (!empty($contentType[0]) && !str_contains($contentType[0], self::JSON)
            && !empty($request->getBody()->getContents())
        ) {
            throw new RequestException('Must be type JSON');
        }

        $acceptHeader = $request->getHeader('Accept');
        if (!empty($acceptHeader[0]) && !str_contains($acceptHeader[0], self::JSON)) {
            throw new RequestException('Must be type JSON');
        }
    }
}