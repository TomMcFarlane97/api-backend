<?php

namespace App\Controller;

use App\Exceptions\ImANumptyException;
use App\Exceptions\RequestException;
use App\Service\UserService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ImANumptyException
     */
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validateRequest($request);
            $this->userService->getAllUsers();
        } catch (RequestException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode());
        } catch (ImANumptyException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode());
        } catch (Throwable $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus(self::INTERNAL_SERVER_ERROR);
        }
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException
     */
    public function getUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException
     */
    public function createUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException
     */
    public function updateUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException
     */
    public function deleteUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED);
    }
}