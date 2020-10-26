<?php

namespace App\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PingController
 * @package App\Controller
 * @codeCoverageIgnore
 */
class PingController extends AbstractController
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return new JsonResponse(['message' => 'success'], self::ACCEPTED, $this->jsonResponseHeader);
    }
}