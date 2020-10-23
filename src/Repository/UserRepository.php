<?php

namespace App\Repository;

use App\Entity\User;

/**
 * Class UserRepository
 * @package App\Repository
 * @method null|User find(int $id): ?User
 */
class UserRepository extends AbstractRepository
{
    protected string $tableName = 'users';

    /** @var array<string, string>  */
    protected array $columns = [
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email_address' => 'setEmailAddress',
    ];

    protected string $primaryKeyName = 'id';
}