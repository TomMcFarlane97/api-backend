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
     * @throws RepositoryException|RequestException|DatabaseException
     */
    public function createUser(array $userBody): User
    {
        return $this->userRepository->createUser($this->mergeUserBodyToObject($userBody));
    }

    /**
     * @param int $userId
     * @param array<string, mixed> $userBody
     * @return User
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function updateUser(int $userId, array $userBody): User
    {
        return $this->userRepository->updateUser(
            $this->mergeUserBodyToObject(
                $userBody,
                true,
                $this->retrieveUser($userId)
            )
        );
    }

    /**
     * @param int $userId
     * @return void
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function deleteUser(int $userId): void
    {
        $this->userRepository->deleteUser(
            $this->retrieveUser($userId)
        );
    }

    /**
     * @param int $userId
     * @return User
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function retrieveUser(int $userId): User
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            throw new RequestException(sprintf('User ID of "%s" was not found', $userId), AbstractController::BAD_REQUEST);
        }
        return $user;
    }

    /**
     * @param array<string, mixed> $userBody
     * @param bool $isUpdate
     * @param User|null $currentUser
     * @return User
     * @throws RepositoryException
     * @throws RequestException
     */
    private function mergeUserBodyToObject(array $userBody, bool $isUpdate = false, User $currentUser = null): User
    {
        $columnSetters = $this->userRepository->getColumnSetters(true);
        $entity = $this->userRepository->getEntityName();
        /** @var User $user */
        $user = $currentUser ? $currentUser : new $entity();
        foreach ($userBody as $columnKey => $columnValue) {
            $this->throwErrorIfNotValidSetter($columnSetters, $columnKey, $entity, $user);
            $user->{$columnSetters[$columnKey]}($columnValue);
            unset($columnSetters[$columnKey]);
        }

        if (!$isUpdate && !empty($columnSetters)) {
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
            throw new RequestException(
                sprintf(
                    'Key name "%s" is not valid, the only valid key names are: %s',
                    $columnKey,
                    $this->userRepository->getColumnKeysAsString(true)
                ),
                AbstractController::UNPROCESSABLE_ENTITY
            );
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
