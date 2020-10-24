<?php

namespace App\Traits;

/**
 * Trait UserDatabaseTrait
 * @package App\Traits
 */
trait UserDatabaseTrait
{
    /** @var array<string, string|null>  */
    protected array $columnSetters = [
        'id' => null,
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email_address' => 'setEmailAddress',
    ];

    /** @var array<string, string>  */
    protected array $columnGetters = [
        'id' => 'getId',
        'first_name' => 'getFirstName',
        'last_name' => 'getLastName',
        'email_address' => 'getEmailAddress',
    ];
}