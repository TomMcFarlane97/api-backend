<?php

namespace App\Controller;

use App\Exceptions\EntityException;
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
     * @throws ImANumptyException|EntityException
     */
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validateRequest($request);
            $users = $this->userService->getAllUsers();
        } catch (RequestException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode())->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        } catch (ImANumptyException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode())->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        } catch (Throwable $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus(self::INTERNAL_SERVER_ERROR)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        }
        $response->getBody()->write(self::jsonEncodeArray($users, true));
        return $response->withStatus(self::ACCEPTED)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     */
    public function getUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequest($request);
            $user = $this->userService->getUserById((int) $args['userId']);
        } catch (RequestException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode())->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        } catch (ImANumptyException $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus($exception->getCode())->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        } catch (Throwable $exception) {
            $response->getBody()->write(self::jsonEncodeArray(['message' => $exception->getMessage()]));
            return $response->withStatus(self::INTERNAL_SERVER_ERROR)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
        }
        $response->getBody()->write(self::jsonEncodeArray($user->convertToArray()));
        return $response->withStatus(self::ACCEPTED)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     */
    public function createUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     */
    public function updateUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     */
    public function deleteUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(self::jsonEncodeArray(['message' => '@todo - configure ' . __METHOD__]));
        return $response->withStatus(self::ACCEPTED)->withHeader(self::HEADER_CONTENT_TYPE, self::JSON);
    }
}