<?php

namespace App\Service;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Interfaces\ConvertToArrayInterface;
use App\Repository\UserRepository;
use App\Traits\BuildEntityLoopTrait;

/**
 * Class UserService
 * @package App\Service
 * @method User buildEntity(string $entityString, array $entityBody, array $columnSetters, bool $isUpdate = false, User $currentEntity = null): User
 */
class UserService
{
    use BuildEntityLoopTrait;

    private UserRepository $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * @return User[]
     * @throws DatabaseException|RepositoryException
     */
    public function getAllUsers(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param int $userId
     * @return ?User
     * @throws DatabaseException|RepositoryException
     */
    public function getUserById(int $userId): ?User
    {
        return $this->repository->find($userId);
    }

    /**
     * @param array<string, mixed> $userBody
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function createUser(array $userBody): User
    {
        return $this->repository->createUser($this->buildEntity(
            $this->repository->getEntityName(),
            $userBody,
            $this->repository->getColumnSetters(true),
        ));
    }

    /**
     * @param int $userId
     * @param array<string, mixed> $userBody
     * @return User
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function updateUser(int $userId, array $userBody): User
    {
        return $this->repository->updateUser(
            $this->buildEntity(
                $this->repository->getEntityName(),
                $userBody,
                $this->repository->getColumnSetters(true),
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
        $this->repository->deleteUser(
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
        $columnSetters = $this->repository->getColumnSetters(true);
        $entity = $this->repository->getEntityName();
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
}
