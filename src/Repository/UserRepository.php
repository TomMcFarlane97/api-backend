<?php

namespace App\Repository;

use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Traits\UserDatabaseTrait;

/**
 * Class UserRepository
 * @package App\Repository
 * @method null|User find(int $id): ?User
 * @method User[] findAll(): User[]
 * @method User insertSingle(string $columnNames, string $columnValues): User
 * @method User updateSingleByPrimaryKey(int $primaryKeyValue, string $updatedValues): User
 * @method null deleteItem(int $primaryKeyValue): User
 * @method null|User findOneBy(array $whereCondition): ?User
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    use UserDatabaseTrait;

    protected string $tableName = 'users';
    protected string $primaryKeyName = 'id';
    protected string $entityName = User::class;

    /**
     * @param User $user
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function createUser(User $user): User
    {
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

    /**
     * @param User $user
     * @return void
     * @throws DatabaseException|RepositoryException
     */
    public function deleteUser(User $user): void
    {
        $this->deleteItem($user->getId());
    }
}
