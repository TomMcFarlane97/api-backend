<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\ErrorResponse;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
use App\Service\UserService;
use JsonException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

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
     * @param AuthenticationService $authenticationService
     * @param LoggerInterface $logger
     * @param UserService $userService
     * @codeCoverageIgnore
     */
    public function __construct(
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        UserService $userService
    ) {
        parent::__construct($authenticationService, $logger);
        $this->userService = $userService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ImANumptyException|EntityException
     * @codeCoverageIgnore
     */
    public function getAllAction(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws EntityException
     * @codeCoverageIgnore
     */
    public function getUserAction(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
        } catch (RequestException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $this->getUser()->convertToArray(),
            StatusCodes::ACCEPTED,
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
    public function createUserAction(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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
            StatusCodes::CREATED,
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
    public function updateUserAction(
        RequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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
            StatusCodes::ACCEPTED,
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
    public function deleteUserAction(
        RequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}
