<?php

namespace App\Entity;

class User
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $emailAddress;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function get_first_name(): string
    {
        return $this->firstName;
    }

    public function set_first_name(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function get_last_name(): string
    {
        return $this->lastName;
    }

    public function set_last_name(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function get_email_address(): string
    {
        return $this->emailAddress;
    }

    public function set_email_address(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

}