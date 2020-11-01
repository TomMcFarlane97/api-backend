<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\RequestMethods;
use App\Helpers\StatusCodes;
use App\Helpers\TokenPayload;
use App\Repository\UserRepository;
use App\Validators\BearerTokenValidator;
use App\Validators\IntegerIDValidator;
use Firebase\JWT\JWT;

class AuthenticationService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string[] $userContents
     * @return string[]
     * @throws DatabaseException|RepositoryException|ImANumptyException
     */
    public function authenticate(array $userContents): array
    {
        return $this->generateTokenContentResponse(
            $this->getUserByContents($userContents)->getId()
        );
    }

    /**
     * @param string[] $authHeaderArray
     * @return string[]
     * @throws DatabaseException|RequestException|ImANumptyException|RepositoryException
     */
    public function refreshToken(array $authHeaderArray): array
    {
        $user = $this->getUserFromBearerToken($authHeaderArray);
        return $this->generateTokenContentResponse($user->getId());
    }

    /**
     * @param array $authHeaderArray
     * @return User
     * @throws DatabaseException|ImANumptyException|RepositoryException|RequestException
     */
    public function getUserFromBearerToken(array $authHeaderArray): User
    {
        $tokenDetails = $this->getTokenDetails($authHeaderArray[0] ?? '');

        $user = $this->userRepository->find(
            (new IntegerIDValidator($tokenDetails['uid'] ?? 0))->getId()
        );

        if (!$user) {
            throw new RequestException('User cannot be found', StatusCodes::BAD_REQUEST);
        }

        return $user;
    }

    public static function isAuthenticationRequired(string $requestedPath, string $requestedMethod): bool
    {
        if (self::shouldExcludeRouteFromAuthentication($requestedPath, $requestedMethod)) {
            return false;
        }
        return !self::shouldExcludeMethodFromAuthentication($requestedMethod);
    }

    private static function shouldExcludeRouteFromAuthentication(string $requestedPath, string $requestedMethod): bool
    {
        foreach (TokenPayload::routesToExclude() as $method => $route) {
            if ($requestedPath === $route && $requestedMethod === $method) {
                return true;
            }
            continue;
        }
        return false;
    }

    private static function shouldExcludeMethodFromAuthentication($requestedMethod): bool
    {
        return in_array($requestedMethod, TokenPayload::methodsToExclude(), true);
    }

    /**
     * @param string $token
     * @return array
     * @throws ImANumptyException
     */
    private function getTokenDetails(string $token): array
    {
        return (new BearerTokenValidator(self::getTokenString($token)))->getToken();
    }

    private static function getTokenString(string $token): string
    {
        return str_replace('Bearer ', '', $token);
    }

    /**
     * @param $userId
     * @return array
     * @throws ImANumptyException
     */
    private function generateTokenContentResponse(int $userId): array
    {
        return [
            TokenPayload::BEARER_TOKEN => $this->generateToken($userId),
            TokenPayload::REFRESH_TOKEN => $this->generateToken($userId, true),
        ];
    }

    /**
     * @param string[] $userContents
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    private function getUserByContents(array $userContents): User
    {
        $user = $this->userRepository->findOneBy($userContents);
        if (!$user) {
            throw new RepositoryException('User does not exist', StatusCodes::BAD_REQUEST);
        }
        return $user;
    }

    /**
     * @param int $userId
     * @param bool $isRefresh
     * @return string
     * @throws ImANumptyException
     */
    private function generateToken(int $userId, bool $isRefresh = false): string
    {
        return JWT::encode(
            TokenPayload::toArray($userId, $isRefresh),
            TokenPayload::getPrivateKey(),
            TokenPayload::getEncodingMethod()
        );
    }
}
