<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractController
{
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    public function getUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    public function createUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    public function updateUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    public function deleteUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }
}