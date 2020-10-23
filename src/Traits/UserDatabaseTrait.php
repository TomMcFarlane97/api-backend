<?php

namespace App\Traits;

/**
 * Trait UserDatabaseTrait
 * @package App\Traits
 *
 * @property array<string, string> $columns
 */
trait UserDatabaseTrait
{
    /** @var array<string, string>  */
    protected array $columns = [
        'id' => '',
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email_address' => 'setEmailAddress',
    ];
}