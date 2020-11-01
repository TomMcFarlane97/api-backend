<?php

namespace App\Interfaces\Repository;

use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;

interface UserRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * @param User $user
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function createUser(User $user): User;

    /**
     * @param User $user
     * @return User
     * @throws DatabaseException|RepositoryException
     */
    public function updateUser(User $user): User;

    /**
     * @param User $user
     * @return void
     * @throws DatabaseException|RepositoryException
     */
    public function deleteUser(User $user): void;
}
