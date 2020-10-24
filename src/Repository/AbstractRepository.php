<?php

namespace App\Repository;

use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Factory\DatabaseFactory;
use App\Interfaces\ConvertToArrayInterface;
use PDO;
use PDOStatement;

/**
 * Class AbstractRepository
 * @package App\Repository
 * @property string $tableName - table name the repository is for
 * @property string $entityName - Type of Object the repository is for
 * @property array<string, string> $columnSetters - Must be key => value for the 'name of column' => 'setter method on object'
 * @property array<string, string> $columnGetters - Must be key => value for the 'name of column' => 'getter method on object'
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
     * @return string
     * @throws RepositoryException
     */
    public function getEntityName(): string
    {
        if (empty($this->entityName)) {
            throw new RepositoryException(sprintf('Entity for table "%s" is not specified in its repository.', $this->getTableName()));
        }
        return $this->entityName;
    }

    /**
     * @param bool $excludePrimaryKey
     * @return array<string, string>
     * @throws RepositoryException
     */
    public function getColumnSetters(bool $excludePrimaryKey = false): array
    {
        $setters = $this->columnSetters;
        if ($excludePrimaryKey) {
            unset($setters[$this->primaryKeyName]);
        }
        if (empty($setters)) {
            throw new RepositoryException(sprintf('There are no columnSetters for "%s" repository', $this->getEntityName()));
        }
        return $setters;
    }

    /**
     * @param bool $excludePrimaryKey
     * @return array<string, string>
     * @throws RepositoryException
     */
    public function getColumnGetters(bool $excludePrimaryKey = false): array
    {
        $getters = $this->columnGetters;
        if ($excludePrimaryKey) {
            unset($getters[$this->primaryKeyName]);
        }
        if (empty($getters)) {
            throw new RepositoryException(sprintf('There are no columnGetters for "%s" repository', $this->getEntityName()));
        }
        return $getters;
    }

    /**
     * @param int $id
     * @return ConvertToArrayInterface|null
     * @throws DatabaseException|RepositoryException
     */
    public function find(int $id): ?ConvertToArrayInterface
    {
        $queryString = sprintf(sprintf(
            'SELECT %s FROM %s WHERE id = %s',
            $this->getColumnKeysAsString(),
            $this->getTableName(),
            $id
        ));
        $query = $this->getPDOStatement($queryString);
        if (!$query->execute()) {
            throw new DatabaseException($query->errorCode());
        }
        $object = $query->fetchObject($this->getEntityName());
        if ($object) {
            return $object;
        }
        return null;
    }

    /**
     * @return object[]
     * @throws DatabaseException|RepositoryException
     */
    public function findAll(): array
    {
        $queryString = sprintf('SELECT %s FROM %s', $this->getColumnKeysAsString(), $this->getTableName());
        $query = $this->getPDOStatement($queryString);
        if (!$query->execute()) {
            throw new DatabaseException($query->errorCode());
        }
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
     * @param bool $excludePrimaryKey
     * @return string
     * @throws RepositoryException
     */
    public function getColumnKeysAsString(bool $excludePrimaryKey = false): string
    {
        $columnsKeys = implode(',', $this->getColumnKeys($excludePrimaryKey));
        if (empty($columnsKeys)) {
            throw new RepositoryException(sprintf('Columns keys are empty for "%s" repository', $this->getEntityName()));
        }
        return $columnsKeys;
    }

    /**
     * @param bool $excludePrimaryKey
     * @return string[]
     * @throws RepositoryException
     */
    protected function getColumnKeys(bool $excludePrimaryKey = false): array
    {
        $keys = $this->columnSetters;
        if ($excludePrimaryKey) {
            unset($keys[$this->primaryKeyName]);
        }
        $columnKeys = array_keys($keys);
        if (empty($columnKeys)) {
            throw new RepositoryException(sprintf('Columns keys are empty for "%s" repository', $this->getEntityName()));
        }

        return $columnKeys;
    }

    /**
     * @param string $columnNames
     * @param string $columnValues
     * @return ConvertToArrayInterface
     * @throws DatabaseException|RepositoryException
     */
    protected function insertSingle(string $columnNames, string $columnValues): ConvertToArrayInterface
    {
        $queryString = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->getTableName(),
            $columnNames,
            $columnValues
        );
        $query = $this->getPDOStatement($queryString);
        if (!$query->execute()) {
            throw new DatabaseException($query->errorCode());
        }

        return $this->find((int) $this->connection->lastInsertId());
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

    /**
     * @param string $queryString
     * @return PDOStatement
     * @throws DatabaseException
     */
    private function getPDOStatement(string $queryString): PDOStatement
    {
        $query = $this->connection->prepare($queryString);
        if (!$query instanceof PDOStatement) {
            throw new DatabaseException(sprintf(
                'Internal Database error on method "%s" and line "%s". Error for query string "%s"',
                __METHOD__,
                __LINE__,
                $queryString
            ));
        }
        return $query;
    }
}