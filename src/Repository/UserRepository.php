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
        $getters = $this->getColumnGetters(true);
        foreach ($this->getColumnKeys(true) as $key) {
            if (empty($getters[$key])) {
                throw new RepositoryException(
                    sprintf('Key "%s" does not exist on the columnGetters', $key),
                    AbstractController::INTERNAL_SERVER_ERROR
                );
            }

            $method = $getters[$key];
            if (!method_exists($user, $method)) {
                throw new RepositoryException(
                    sprintf('Getter "%s" does not exist on the entity "%s"', $method, get_class($user)),
                    AbstractController::INTERNAL_SERVER_ERROR
                );
            }
            $columnValues = $columnValues . "'" . $user->{$method}() . "',";
        }
        return $this->insertSingle(
            $this->getColumnKeysAsString(true),
            rtrim($columnValues, ',')
        );
    }
}