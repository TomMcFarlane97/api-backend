<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\Environment;
use App\Helpers\ErrorResponse;
use App\Helpers\ResponseHeaders;
use App\Helpers\StatusCodes;
use App\Interfaces\ConvertToArrayInterface;
use App\Service\AuthenticationService;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UnexpectedValueException;

abstract class AbstractController extends ResponseHeaders
{
    /** @var string[] */
    protected array $jsonResponseHeader = [self::HEADER_CONTENT_TYPE => self::JSON];

    protected AuthenticationService $authenticationService;
    protected LoggerInterface $logger;

    public function __construct(AuthenticationService $authenticationService, LoggerInterface $logger)
    {
        $this->authenticationService = $authenticationService;
        $this->logger = $logger;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    protected function validateRequest(RequestInterface $request): ?ResponseInterface
    {
        $this->validateRequestIsJson($request);

        if (!$this->isAuthenticationRequired($request->getUri()->getPath(), $request->getMethod())) {
            return null;
        }

        return $this->validateAuthentication($request->getHeader(ResponseHeaders::HEADER_AUTHORIZATION));
    }

    /**
     * @param Throwable $exception
     * @return string[]
     */
    protected function getMessage(Throwable $exception): array
    {
        $message = ['message' => $exception->getMessage()];
        if (!Environment::isProduction()) {
            $message = array_merge($message, $exception->getTrace());
        }
        return $message;
    }

    /**
     * @param array<ConvertToArrayInterface|array<string>|string> $entities
     * @return array<int, array<string, mixed>>
     * @throws EntityException|ImANumptyException
     */
    protected static function convertObjectToArray(array $entities): array
    {
        $message = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof ConvertToArrayInterface) {
                throw new ImANumptyException(
                    sprintf('Can you even code bro. Method - %s', __METHOD__),
                    StatusCodes::TEA_POT
                );
            }
            $message[] = $entity->convertToArray();
        }

        return $message;
    }

    /**
     * @param RequestInterface $request
     * @throws RequestException
     */
    private function validateRequestIsJson(RequestInterface $request): void
    {
        if (
        $this->shouldRequestHaveContentTypeHeader(
            $request->getHeader(self::HEADER_CONTENT_TYPE),
            $request->getBody()->getContents()
        )
        ) {
            throw new RequestException(
                sprintf('Request header "%s" must be type "%s"', self::HEADER_CONTENT_TYPE, self::JSON),
                StatusCodes::UNSUPPORTED_MIME_TYPE
            );
        }

        $acceptHeader = $request->getHeader(self::HEADER_ACCEPT);
        if (!empty($acceptHeader[0]) && !str_contains($acceptHeader[0], self::JSON)) {
            throw new RequestException(
                sprintf('Request header "%s" must be type "%s"', self::HEADER_ACCEPT, self::JSON),
                StatusCodes::BAD_REQUEST
            );
        }
    }

    /**
     * @param string[] $contentType
     * @param string $bodyContents
     * @return bool
     */
    private function shouldRequestHaveContentTypeHeader(array $contentType, string $bodyContents): bool
    {
        return !empty($contentType[0]) && !str_contains($contentType[0], self::JSON)
            && !empty($bodyContents);
    }

    /**
     * @param string $path
     * @param string $method
     * @return bool
     */
    private function isAuthenticationRequired(string $path, string $method): bool
    {
        return AuthenticationService::isAuthenticationRequired($path, $method);
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
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return new ErrorResponse(
                json_encode(['message' => $exception->getMessage()]),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        } catch (
            BeforeValidException | ExpiredException | SignatureInvalidException | UnexpectedValueException $exception
        ) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return new ErrorResponse(
                json_encode(['message' => $exception->getMessage()]),
                StatusCodes::UNAUTHORIZED,
                $this->jsonResponseHeader
            );
        }

        return null;
    }
}
