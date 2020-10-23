<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return User[]
     * @throws DatabaseException|RepositoryException
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param int $userId
     * @return ?User
     * @throws DatabaseException|RepositoryException
     */
    public function getUserById(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }
}