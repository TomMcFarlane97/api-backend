<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\ErrorResponse;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
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
class AuthenticationController extends AbstractController
{
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
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ImANumptyException
     * @codeCoverageIgnore
     */
    public function refresh(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
            $tokens = $this->authenticationService->refreshToken($request->getHeader(self::HEADER_AUTHORIZATION));
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
