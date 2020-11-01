<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
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
     * @param array $authHeaderArray
     * @return array
     * @throws ImANumptyException
     */
    public function validateToken(array $authHeaderArray): array
    {
        return (new BearerTokenValidator(
            self::getTokenString($authHeaderArray[0] ?? '')
        ))->getToken();
    }

    /**
     * @param string[] $authHeaderArray
     * @return string[]
     * @throws DatabaseException|RequestException|ImANumptyException|RepositoryException
     */
    public function refreshToken(array $authHeaderArray): array
    {
        $tokenDetails = $this->validateToken($authHeaderArray);
        $user = $this->userRepository->find(
            (new IntegerIDValidator($tokenDetails['uid'] ?? 0))->getId()
        );

        if (!$user) {
            throw new RequestException('User cannot be found', StatusCodes::BAD_REQUEST);
        }

        return $this->generateTokenContentResponse($user->getId());
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
