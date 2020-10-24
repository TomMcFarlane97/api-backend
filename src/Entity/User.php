<?php

namespace App\Entity;

use App\Exceptions\RequestException;
use App\Interfaces\ConvertToArrayInterface;
use App\Traits\ConvertToArrayTrait;
use App\Traits\UserDatabaseTrait;
use App\Validators\EmailAddressValidator;

/**
 * Class User
 * @package App\Entity
 */
class User implements ConvertToArrayInterface
{
    private int $id;
    private string $first_name;
    private string $last_name;
    private string $email_address;

    use UserDatabaseTrait, ConvertToArrayTrait;

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

    /**
     * @param string $emailAddress
     * @throws RequestException
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->email_address = (new EmailAddressValidator($emailAddress))->getEmailAddress();
    }
}