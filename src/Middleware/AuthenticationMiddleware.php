<?php

namespace App\Middleware;

use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\RequestMethods;
use App\Helpers\ResponseHeaders;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
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
        $method = $request->getMethod();
        if ($method === RequestMethods::OPTIONS) {
            $this->logger->error('dont have to authenticate');
            return $handler->handle($request);
        }
        $isAuthenticationRequired = AuthenticationService::isAuthenticationRequired(
            $request->getUri()->getPath(),
            $method
        );

        if (!$isAuthenticationRequired) {
            return $handler->handle($request);
        }

        $responseMessage = $this->validateAuthentication($request->getHeader(ResponseHeaders::HEADER_AUTHORIZATION));
        if (!empty($responseMessage)) {
            $this->logger->error('error response' . ' ' . __LINE__);
            $response = $handler->handle($request);
            $response->getBody()->write(json_encode([$responseMessage['message']]));
            return $response->withStatus($responseMessage['code']);
        }

        return $handler->handle($request);
    }

    /**
     * @param string[] $authenticationHeader
     * @return array|null
     */
    private function validateAuthentication(array $authenticationHeader): array
    {
        try {
            $this->authenticationService->getUserFromBearerToken($authenticationHeader);
        } catch (DatabaseException | ImANumptyException | RepositoryException | RequestException $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return ['message' => $exception->getMessage(), 'code' => $exception->getCode()];
        } catch (
            BeforeValidException | ExpiredException | SignatureInvalidException | UnexpectedValueException $exception
        ) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return ['message' => '', 'code' => StatusCodes::UNAUTHORIZED];
        }

        return [];
    }
}
