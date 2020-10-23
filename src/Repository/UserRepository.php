<?php

namespace App\Repository;

use App\Entity\User;
use App\Traits\UserDatabaseTrait;

/**
 * Class UserRepository
 * @package App\Repository
 * @method null|User find(int $id): ?User
 * @method User[] findAll(): User[]
 */
class UserRepository extends AbstractRepository
{
    protected string $tableName = 'users';
    protected string $primaryKeyName = 'id';
    protected string $entityName = User::class;

    use UserDatabaseTrait;
}