<?php

namespace App\Repository;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Traits\UserDatabaseTrait;

/**
 * Class UserRepository
 * @package App\Repository
 * @method null|User find(int $id): ?User
 * @method User[] findAll(): User[]
 * @method User insertSingle(string $columnNames, string $columnValues): User
 * @method User updateSingleByPrimaryKey(int $primaryKeyValue, string $updatedValues): User
 */
class UserRepository extends AbstractRepository
{
    protected string $tableName = 'users';
    protected string $primaryKeyName = 'id';
    protected string $entityName = User::class;

    use UserDatabaseTrait;

    /**
     * @param User $user
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function createUser(User $user): User
    {
        $columnValues = '';
        return $this->insertSingle(
            $this->getColumnKeysAsString(true),
            $this->getColumnValues($user)
        );
    }

    /**
     * @param User $user
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function updateUser(User $user): User
    {
        return $this->updateSingleByPrimaryKey(
            $user->getId(),
            $this->getColumnValues($user, true)
        );
    }
}