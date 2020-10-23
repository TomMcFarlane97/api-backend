<?php

namespace App\Repository;

use App\Entity\User;

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

    /** @var array<string, string>  */
    protected array $columns = [
        'id' => '',
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email_address' => 'setEmailAddress',
    ];
}