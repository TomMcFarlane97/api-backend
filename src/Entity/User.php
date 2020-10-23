<?php

namespace App\Entity;

use App\Exceptions\EntityException;
use App\Interfaces\ConvertToArrayInterface;
use App\Traits\UserDatabaseTrait;

/**
 * Class User
 * @package App\Entity
 * @todo - figure out how to "magically" convert snake case db characters to camelCase
 */
class User implements ConvertToArrayInterface
{
    private int $id;
    private string $first_name;
    private string $last_name;
    private string $email_address;

    use UserDatabaseTrait;

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

    /**
     * @return array<string, mixed>
     * @throws EntityException
     */
    public function convertToArray(): array
    {
        $transformer = [];
        foreach (array_keys($this->columns) as $key) {
            if (empty($this->{$key})) {
                throw new EntityException(
                    sprintf('"%s" Entity does not contain this column in the trait "%s"', __CLASS__, $key)
                );
            }
            $transformer[$key] = $this->{$key};
        }
        return $transformer;
    }
}