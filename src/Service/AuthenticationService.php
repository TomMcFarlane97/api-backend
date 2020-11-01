<?php

namespace App\Service;

use App\Exceptions\DatabaseException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Helpers\StatusCodes;
use App\Helpers\TokenPayload;
use App\Repository\UserRepository;
use App\Validators\BearerTokenValidator;
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
     * @return string
     * @throws DatabaseException|RepositoryException|ImANumptyException
     */
    public function authenticate(array $userContents): string
    {
        $user = $this->userRepository->findOneBy($userContents);
        if (!$user) {
            throw new RepositoryException('User does not exist', StatusCodes::BAD_REQUEST);
        }
        return $this->generateToken($user->getId());
    }

    public function validateToken(array $authHeaderArray): void
    {
        $token = new BearerTokenValidator($bearerToken[0] ?? '');
    }

    /**
     * @param int $userId
     * @return string
     * @throws ImANumptyException
     */
    private function generateToken(int $userId): string
    {
        return JWT::encode(
            TokenPayload::toArray($userId),
            TokenPayload::getPrivateKey(),
            TokenPayload::getEncodingMethod()
        );
    }
}
