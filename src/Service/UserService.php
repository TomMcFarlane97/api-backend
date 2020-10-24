<?php

namespace App\Service;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Interfaces\ConvertToArrayInterface;
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

    /**
     * @param array<string, mixed> $userBody
     * @return User
     * @throws RepositoryException|RequestException
     */
    public function createUser(array $userBody): User
    {
        return $this->userRepository->createUser($this->mergeUserBodyToObject($userBody));
    }

    /**
     * @param array<string, mixed> $userBody
     * @return User
     * @throws RepositoryException|RequestException
     */
    private function mergeUserBodyToObject(array $userBody): User
    {
        $columnSetters = $this->userRepository->getColumnSetters(true);
        $entity = $this->userRepository->getEntityName();
        /** @var User $user */
        $user = new $entity();
        foreach ($userBody as $columnKey => $columnValue) {
            $this->throwErrorIfNotValidSetter($columnSetters, $columnKey, $entity, $user);
            $user->{$columnSetters[$columnKey]}($columnValue);
            unset($columnSetters[$columnKey]);
        }

        if (!empty($columnSetters)) {
            throw new RequestException(
                sprintf(
                    'Body of request does not contain the following values %s',
                    implode(',', array_keys($columnSetters))
                ),
                AbstractController::UNPROCESSABLE_ENTITY
            );
        }

        return $user;
    }

    /**
     * @param string[] $columnSetters
     * @param string $columnKey
     * @param string $entityString
     * @param ConvertToArrayInterface $entityObject
     * @throws RepositoryException|RequestException
     */
    private function throwErrorIfNotValidSetter(
        array $columnSetters,
        string $columnKey,
        string $entityString,
        ConvertToArrayInterface $entityObject
    ): void {
        if (empty($columnSetters[$columnKey])) {
            throw new RequestException(sprintf(
                'Key name "%s" is not valid, the only valid key names are: %s',
                $columnKey,
                $this->userRepository->getColumnKeysAsString()
            ));
        }

        $method = $columnSetters[$columnKey];
        if (!method_exists($entityObject, $method)) {
            throw new RepositoryException(
                sprintf(
                    'Method "%s" does not exist on entity "%s". Check the set up for its repository.',
                    $method,
                    $entityString
                ),
                AbstractController::NOT_IMPLEMENTED);
        }
    }
}