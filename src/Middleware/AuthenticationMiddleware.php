<?php

namespace App\Middleware;

use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\ResponseHeaders;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class AuthenticationMiddleware
{
    private AuthenticationService $authenticationService;
    private LoggerInterface $logger;

    public function __construct(AuthenticationService $authenticationService, LoggerInterface $logger)
    {
        $this->authenticationService = $authenticationService;
        $this->logger = $logger;
    }

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $isAuthenticationRequired = AuthenticationService::isAuthenticationRequired(
            $request->getUri()->getPath(),
            $request->getMethod()
        );

        if (!$isAuthenticationRequired) {
            return $handler->handle($request);
        }

        $response = $this->validateAuthentication($request->getHeader(ResponseHeaders::HEADER_AUTHORIZATION));
        if ($response) {
            return $response;
        }

        return $handler->handle($request);
    }

    /**
     * @param string[] $authenticationHeader
     * @return ResponseInterface|null
     */
    private function validateAuthentication(array $authenticationHeader): ?ResponseInterface
    {
        try {
            $this->authenticationService->getUserFromBearerToken($authenticationHeader);
        } catch (DatabaseException | ImANumptyException | RepositoryException | RequestException $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        } catch (
            BeforeValidException | ExpiredException | SignatureInvalidException | UnexpectedValueException $exception
        ) {
            return new JsonResponse([], StatusCodes::UNAUTHORIZED);
        }

        return null;
    }
}
