<?php

namespace App\Traits;

/**
 * Trait NoteDatabaseTrait
 * @package App\Traits
 */
trait NoteDatabaseTrait
{
    /** @var array<string, string|null>  */
    protected array $columnSetters = [
        'id' => null,
        'note' => 'setNote',
        'user_id' => 'setUser',
    ];

    /** @var array<string, string>  */
    protected array $columnGetters = [
        'id' => 'getId',
        'note' => 'getNote',
        'user_id' => 'getUser',
    ];
}