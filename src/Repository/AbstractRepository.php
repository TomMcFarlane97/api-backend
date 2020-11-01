<?php

namespace App\Repository;

use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Factory\DatabaseFactory;
use App\Helpers\StatusCodes;
use App\Interfaces\ConvertToArrayInterface;
use App\Interfaces\Repository\AbstractRepositoryInterface;
use PDO;
use PDOStatement;

/**
 * Class AbstractRepository
 * @package App\Repository
 * @property string $tableName - table name the repository is for
 * @property string $entityName - Type of Object the repository is for
 * @property array<string, string> $columnSetters - Must be key => value for the 'name of column' => 'setter method'
 * @property array<string, string> $columnGetters - Must be key => value for the 'name of column' => 'getter method'
 * @property string $primaryKeyName - Column name of primary key
 * @property array<string, string> $foreignKeys - Column name of foreign keys in an array
 */
abstract class AbstractRepository implements AbstractRepositoryInterface
{
    /** @var array<string, string> */
    protected array $foreignKeys = [];
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
            throw new RepositoryException(
                sprintf('Entity for table "%s" is not specified in its repository.', $this->getTableName())
            );
        }
        return $this->entityName;
    }

    /**
     * @param bool $excludePrimaryKey
     * @param bool $excludeForeignKeys
     * @return array<string, string>
     * @throws RepositoryException
     */
    public function getColumnSetters(bool $excludePrimaryKey = false, bool $excludeForeignKeys = false): array
    {
        $setters = $this->columnSetters;
        if ($excludePrimaryKey) {
            unset($setters[$this->primaryKeyName]);
        }

        if ($excludeForeignKeys) {
            foreach ($this->getForeignKeys() as $foreignKey) {
                unset($setters[$foreignKey]);
            }
        }
        if (empty($setters)) {
            throw new RepositoryException(
                sprintf('There are no columnSetters for "%s" repository', $this->getEntityName())
            );
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
            throw new RepositoryException(
                sprintf('There are no columnGetters for "%s" repository', $this->getEntityName())
            );
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
        $object = $this->getPDOStatementAndExecute($queryString)
            ->fetchObject($this->getEntityName());
        if ($object) {
            return $object;
        }
        return null;
    }

    /**
     * @return ConvertToArrayInterface[]
     * @throws DatabaseException|RepositoryException
     */
    public function findAll(): array
    {
        $queryString = sprintf('SELECT %s FROM %s', $this->getColumnKeysAsString(), $this->getTableName());
        $query = $this->getPDOStatementAndExecute($queryString)
            ->fetchAll(PDO::FETCH_CLASS, $this->getEntityName());
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
            throw new RepositoryException(
                sprintf('Columns keys are empty for "%s" repository', $this->getEntityName())
            );
        }
        return $columnsKeys;
    }

    /**
     * @param array<string, string|int> $whereConditions
     * @param int|null $limit
     * @return ConvertToArrayInterface[]
     * @throws RepositoryException|DatabaseException
     */
    public function findBy(array $whereConditions = [], int $limit = null): array
    {
        $queryString = sprintf(
            'SELECT %s FROM %s WHERE %s %s',
            $this->getColumnKeysAsString(),
            $this->getTableName(),
            $this->getSearchFromConditions($whereConditions, true),
            $limit ? ' LIMIT ' . $limit : ''
        );
        $query = $this->getPDOStatementAndExecute($queryString)
            ->fetchAll(PDO::FETCH_CLASS, $this->getEntityName());
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
     * @param array<string, string|int> $whereConditions
     * @return ConvertToArrayInterface
     * @throws RepositoryException|DatabaseException
     */
    public function findOneBy(array $whereConditions = []): ?ConvertToArrayInterface
    {
        $queryString = sprintf(
            'SELECT %s FROM %s WHERE %s %s',
            $this->getColumnKeysAsString(),
            $this->getTableName(),
            $this->getSearchFromConditions($whereConditions, true),
            ' LIMIT 1'
        );
        $query = $this->getPDOStatementAndExecute($queryString)
            ->fetchObject($this->getEntityName());
        if ($query === false) {
            return null;
        }
        return $query;
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
            throw new RepositoryException(
                sprintf('Columns keys are empty for "%s" repository', $this->getEntityName())
            );
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
        $this->getPDOStatementAndExecute($queryString);
        return $this->find((int) $this->connection->lastInsertId());
    }

    /**
     * @param int $primaryKeyValue
     * @return void
     * @throws DatabaseException|RepositoryException
     */
    protected function deleteItem(int $primaryKeyValue): void
    {
        $queryString = sprintf(
            'DELETE FROM %s WHERE %s = %s',
            $this->getTableName(),
            $this->primaryKeyName,
            $primaryKeyValue
        );
        $this->getPDOStatementAndExecute($queryString);
        if ($this->find($primaryKeyValue)) {
            throw new DatabaseException(
                sprintf(
                    'Row with primary key value of "%s" has not been deleted from table "%s"',
                    $primaryKeyValue,
                    $this->getTableName()
                ),
                StatusCodes::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param int $primaryKeyValue
     * @param string $updatedValues
     * @return ConvertToArrayInterface
     * @throws DatabaseException|RepositoryException
     */
    protected function updateSingleByPrimaryKey(int $primaryKeyValue, string $updatedValues): ConvertToArrayInterface
    {
        $queryString = sprintf(
            'UPDATE %s SET %s WHERE %s = %s',
            $this->getTableName(),
            $updatedValues,
            $this->primaryKeyName,
            $primaryKeyValue
        );
        $this->getPDOStatementAndExecute($queryString);
        return $this->find($primaryKeyValue);
    }

    /**
     * @param ConvertToArrayInterface $entity
     * @param bool $entityExists
     * @return string
     * @throws RepositoryException
     */
    protected function getColumnValues(ConvertToArrayInterface $entity, bool $entityExists = false): string
    {
        $columnValues = '';
        $getters = $this->getColumnGetters(true);
        foreach ($this->getColumnKeys(true) as $key) {
            if (empty($getters[$key])) {
                throw new RepositoryException(
                    sprintf('Key "%s" does not exist on the columnGetters', $key),
                    StatusCodes::INTERNAL_SERVER_ERROR
                );
            }

            $method = $getters[$key];
            if (!method_exists($entity, $method)) {
                throw new RepositoryException(
                    sprintf('Getter "%s" does not exist on the entity "%s"', $method, get_class($entity)),
                    StatusCodes::INTERNAL_SERVER_ERROR
                );
            }

            $columnValues = $entityExists ?
                $columnValues . " " . $key . " = '" . $entity->{$method}() . "',"
                : $columnValues . "'" . $entity->{$method}() . "',";
        }
        return rtrim($columnValues, ',');
    }

    /**
     * @param array<string, string|int> $conditions
     * @param bool $isAndClause
     * @return string
     * @throws RepositoryException
     */
    protected function getSearchFromConditions(array $conditions, bool $isAndClause = false): string
    {
        $joinCondition = $isAndClause ? ' AND ' : ' OR ';
        $condition = '';
        foreach ($this->getColumnKeys() as $key) {
            if (!isset($conditions[$key])) {
                continue;
            }

            $condition = $condition . $key . ' = ' . '"' . $conditions[$key] . '"' . $joinCondition;
            unset($conditions[$key]);
        }

        if (!empty($conditions)) {
            throw new RepositoryException(
                sprintf('Unknown columns in query "%s"', implode(',', array_keys($conditions))),
                StatusCodes::INTERNAL_SERVER_ERROR
            );
        }

        return rtrim($condition, $joinCondition);
    }

    /**
     * @return string
     * @throws RepositoryException
     */
    private function getTableName(): string
    {
        if (empty($this->tableName)) {
            throw new RepositoryException(
                'A repository you have just made does not contain a property for the $tableName'
            );
        }
        return $this->tableName;
    }

    /**
     * @param string $queryString
     * @return PDOStatement
     * @throws DatabaseException
     */
    private function getPDOStatementAndExecute(string $queryString): PDOStatement
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
        if (!$query->execute()) {
            throw new DatabaseException(
                sprintf($query->errorCode() . ' BAD SQL - "%s"', $queryString),
                StatusCodes::INTERNAL_SERVER_ERROR
            );
        }
        return $query;
    }

    /**
     * @return string[]
     */
    private function getForeignKeys(): array
    {
        return array_values($this->foreignKeys);
    }
}
