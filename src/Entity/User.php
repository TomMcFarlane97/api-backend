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

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $firstName): void
    {
        $this->first_name = $firstName;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $lastName): void
    {
        $this->last_name = $lastName;
    }

    public function getEmailAddress(): string
    {
        return $this->email_address;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->email_address = $emailAddress;
    }

}