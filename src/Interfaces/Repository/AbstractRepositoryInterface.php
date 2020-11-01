<?php

namespace App\Interfaces\Repository;

use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Interfaces\ConvertToArrayInterface;

interface AbstractRepositoryInterface
{
    /**
     * @return string
     * @throws RepositoryException
     */
    public function getEntityName(): string;

    /**
     * @param bool $excludePrimaryKey
     * @param bool $excludeForeignKeys
     * @return array<string, string>
     * @throws RepositoryException
     */
    public function getColumnSetters(bool $excludePrimaryKey = false, bool $excludeForeignKeys = false): array;

    /**
     * @param bool $excludePrimaryKey
     * @return array<string, string>
     * @throws RepositoryException
     */
    public function getColumnGetters(bool $excludePrimaryKey = false): array;

    /**
     * @param int $id
     * @return ConvertToArrayInterface|null
     * @throws DatabaseException|RepositoryException
     */
    public function find(int $id): ?ConvertToArrayInterface;

    /**
     * @return ConvertToArrayInterface[]
     * @throws DatabaseException|RepositoryException
     */
    public function findAll(): array;

    /**
     * @param bool $excludePrimaryKey
     * @return string
     * @throws RepositoryException
     */
    public function getColumnKeysAsString(bool $excludePrimaryKey = false): string;

    /**
     * @param array<string, string|int> $whereConditions
     * @param int|null $limit
     * @return ConvertToArrayInterface[]
     * @throws RepositoryException|DatabaseException
     */
    public function findBy(array $whereConditions = [], int $limit = null): array;

    /**
     * @param array<string, string|int> $whereConditions
     * @return ConvertToArrayInterface
     * @throws RepositoryException|DatabaseException
     */
    public function findOneBy(array $whereConditions = []): ?ConvertToArrayInterface;
}
