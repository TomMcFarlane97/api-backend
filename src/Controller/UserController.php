<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Service\UserService;
use JsonException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserController
 * @package App\Controller
 * @codeCoverageIgnore
 */
class UserController extends AbstractController
{
    private UserService $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @codeCoverageIgnore
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     * @codeCoverageIgnore
     */
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $users = $this->userService->getAllUsers();
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
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
     * @codeCoverageIgnore
     */
    public function getUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->userService->getUserById((int) $args['userId']);
            if (!$user) {
                throw new RequestException(sprintf('User ID "%s" does not exist', $args['userId']), self::BAD_REQUEST);
            }
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
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
     * @throws EntityException|JsonException
     * @codeCoverageIgnore
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
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            self::CREATED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException|JsonException
     * @codeCoverageIgnore
     */
    public function updateUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->userService->updateUser(
                (int) $args['userId'],
                json_decode(
                    $request->getBody()->getContents(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
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
     * @codeCoverageIgnore
     */
    public function deleteUser(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $this->userService->deleteUser((int) $args['userId']);
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}
