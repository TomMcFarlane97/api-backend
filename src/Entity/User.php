<?php

namespace App\Entity;

/**
 * Class User
 * @package App\Entity
 * @todo - figure out how to "magically" convert snake case db characters to camelCase
 */
class User
{
    private int $id;
    private string $first_name;
    private string $last_name;
    private string $email_address;

    public function getId(): int
    {
        return $this->id;
    }

    public function get_first_name(): string
    {
        return $this->first_name;
    }

    public function set_first_name(string $firstName): void
    {
        $this->first_name = $firstName;
    }

    public function get_last_name(): string
    {
        return $this->last_name;
    }

    public function set_last_name(string $lastName): void
    {
        $this->last_name = $lastName;
    }

    public function get_email_address(): string
    {
        return $this->email_address;
    }

    public function set_email_address(string $emailAddress): void
    {
        $this->email_address = $emailAddress;
    }

}