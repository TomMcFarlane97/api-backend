<?php

namespace App\Repository;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
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
     * @throws DatabaseException
     */
    public function find(int $id): ?object
    {
        $query = $this->connection->query(sprintf('SELECT * FROM %s WHERE id = :id', $this->tableName));
        if (!$query) {
            throw new DatabaseException(sprintf('Internal Database error on method "%s" and line "%s"', __METHOD__, __LINE__));
        }
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject($this->entityName);
    }

    /**
     * @return object[]
     * @throws DatabaseException
     */
    public function findAll(): array
    {
        $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $query = $this->connection->query(sprintf('SELECT %s FROM %s', implode(',', array_keys($this->columns)), $this->tableName));
        if (!$query) {
            throw new DatabaseException(sprintf('Internal Database error on method "%s" and line "%s"', __METHOD__, __LINE__));
        }
        $query->execute();
        $query = $query->fetchAll(PDO::FETCH_CLASS, $this->entityName);
        if ($query === false) {
            throw new DatabaseException(sprintf(
                'Internal Database error trying to retrieve the results on method "%s" and line "%s"',
                __METHOD__,
                __LINE__
            ));
        }
        return $query;
    }
}