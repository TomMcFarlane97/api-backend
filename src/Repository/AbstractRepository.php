<?php

namespace App\Repository;

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
     * @throws RepositoryException|EntityException
     */
    public function find(int $id): ?object
    {
        $query = $this->connection->query(sprintf('SELECT * %s WHERE id = :id', $this->tableName));
        $query->bindValue(':id', $id);
        $query->execute();
        $entity = $query->fetch();
        return $this->mapRowToEntity($entity);
    }

    /**
     * @param ?|array $entityValues
     * @return object
     * @throws RepositoryException|EntityException
     */
    private function mapRowToEntity(?array $entityValues): object
    {
        $entity = new $this->entityName($entityValues[$this->primaryKeyName]);
        foreach ($entityValues as $key => $value) {
            if (empty($this->columns[$key])) {
                throw new EntityException(
                    sprintf(
                        'Key "%s" does not exist for entity "%s" inside the columns "%s". Please add it to the columns',
                        $key,
                        $this->entityName,
                        implode(', ', $this->columns)
                    )
                );
            }
            $methodName = $this->columns[$key];
            if (!method_exists($entity, $methodName)) {
                throw new RepositoryException(
                    sprintf('Class "%s" does not contain method "%s"', $this->entityName, $methodName)
                );
            }
            $entity->{$methodName}($value);
        }

        return $entity;
    }
}