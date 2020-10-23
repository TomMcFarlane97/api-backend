<?php

namespace App\Repository;

use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Factory\DatabaseFactory;
use PDO;

/**
 * Class AbstractRepository
 * @package App\Repository
 * @property string $tableName - table name the repository is for
 * @property string $entityName - Type of Object the repository is for
 * @property array<string, string> $columns - Must be key => value for the 'name of column' => 'setter method on object'
 * @property string $primaryKeyName - Column name of primary key
 */
abstract class AbstractRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = DatabaseFactory::getDatabase()->getConnection();
    }

    /**
     * @param int $id
     * @return object|null
     * @throws DatabaseException|RepositoryException
     */
    public function find(int $id): ?object
    {
        $query = $this->connection->query(sprintf(
            'SELECT %s FROM %s WHERE id = %s',
            $this->getColumnKeys(),
            $this->getTableName(),
            $id
        ));
        if (!$query) {
            throw new DatabaseException(sprintf('Internal Database error on method "%s" and line "%s"', __METHOD__, __LINE__));
        }
        $query->execute();
        return $query->fetchObject($this->getEntityName());
    }

    /**
     * @return object[]
     * @throws DatabaseException|RepositoryException
     */
    public function findAll(): array
    {
        $query = $this->connection->query(sprintf('SELECT %s FROM %s', $this->getColumnKeys(), $this->getTableName()));
        if (!$query) {
            throw new DatabaseException(sprintf('Internal Database error on method "%s" and line "%s"', __METHOD__, __LINE__));
        }
        $query->execute();
        $query = $query->fetchAll(PDO::FETCH_CLASS, $this->getEntityName());
        if ($query === false) {
            throw new DatabaseException(sprintf(
                'Internal Database error trying to retrieve the results on method "%s" and line "%s"',
                __METHOD__,
                __LINE__
            ));
        }
        return $query;
    }

    /**
     * @return string
     * @throws RepositoryException
     */
    private function getColumnKeys(): string
    {
        $columnsKeys = implode(',', array_keys($this->columns));
        if (empty($columnsKeys)) {
            throw new RepositoryException(sprintf('Columns keys are empty for "%s" repository', $this->getEntityName()));
        }
        return $columnsKeys;
    }

    /**
     * @return string
     * @throws RepositoryException
     */
    private function getEntityName(): string
    {
        if (empty($this->entityName)) {
            throw new RepositoryException(sprintf('Entity for table "%s" is not specified in its repository.', $this->getTableName()));
        }
        return $this->entityName;
    }

    /**
     * @return string
     * @throws RepositoryException
     */
    private function getTableName(): string
    {
        if (empty($this->tableName)) {
            throw new RepositoryException('A repository you have just made does not contain a property for the $tableName');
        }
        return $this->tableName;
    }
}