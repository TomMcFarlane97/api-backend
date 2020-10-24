<?php

namespace App\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class NoteController extends AbstractController
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function getAll(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(
            ['message' => '@todo - populate ' . __METHOD__],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function createNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(
            ['message' => '@todo - populate ' . __METHOD__],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function getNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(
            ['message' => '@todo - populate ' . __METHOD__],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function updateNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(
            ['message' => '@todo - populate ' . __METHOD__],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function deleteNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(
            ['message' => '@todo - populate ' . __METHOD__],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}