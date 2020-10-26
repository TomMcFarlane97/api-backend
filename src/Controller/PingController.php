<?php

namespace App\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PingController extends AbstractController
{
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return new JsonResponse(['message' => 'success'], self::ACCEPTED, $this->jsonResponseHeader);
    }
}