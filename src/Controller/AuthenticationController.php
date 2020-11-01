<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
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
class AuthenticationController extends AbstractController
{
    private AuthenticationService $authenticationService;

    /**
     * UserController constructor.
     * @param AuthenticationService $authenticationService
     * @codeCoverageIgnore
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ImANumptyException
     * @codeCoverageIgnore
     */
    public function login(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $tokens = $this->authenticationService->authenticate(json_decode(
                $request->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            ));
        } catch (RequestException | DatabaseException | RepositoryException | JsonException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }

        return new JsonResponse(
            $tokens,
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}
