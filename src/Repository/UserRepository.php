<?php

namespace App\Repository;

class UserRepository extends AbstractRepository
{
    protected string $tableName = 'users';

    protected array $columns = [
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email_address' => 'setEmailAddress',
    ];

    protected string $primaryKeyName = 'id';
}