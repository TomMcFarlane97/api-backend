<?php

namespace App\Controller;

use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RequestException;
use App\Service\UserService;
use Laminas\Diactoros\Response\JsonResponse;
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
            $this->validateRequestIsJson($request);
            $users = $this->userService->getAllUsers();
        } catch (RequestException $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        } catch (Throwable $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                self::INTERNAL_SERVER_ERROR,
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [self::convertObjectToArray($users)],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException
     */
    public function getUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->userService->getUserById((int) $args['userId']);
        } catch (RequestException $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        } catch (Throwable $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                self::INTERNAL_SERVER_ERROR,
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws EntityException
     */
    public function createUser(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->userService->createUser(json_decode(
                $request->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            ));
        } catch (RequestException $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        } catch (Throwable $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                self::INTERNAL_SERVER_ERROR,
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
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
    public function updateUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(['message' => '@todo - configure ' . __METHOD__], self::ACCEPTED, $this->jsonResponseHeader);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function deleteUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return new JsonResponse(['message' => '@todo - configure ' . __METHOD__], self::ACCEPTED, $this->jsonResponseHeader);
    }
}