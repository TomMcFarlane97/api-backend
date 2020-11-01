<?php

namespace App\Traits;

use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\StatusCodes;
use App\Interfaces\ConvertToArrayInterface;
use App\Repository\AbstractRepository;

/**
 * Trait BuildEntityLoop
 * @package App\Traits
 * @property AbstractRepository $repository
 */
trait BuildEntityLoopTrait
{
    /**
     * @param string $entityString
     * @param array<string, string|int> $entityBody
     * @param string[] $columnSetters
     * @param bool $isUpdate
     * @param ConvertToArrayInterface|null $currentEntity
     * @return ConvertToArrayInterface
     * @throws RepositoryException|RequestException
     */
    private function buildEntity(
        string $entityString,
        array $entityBody,
        array $columnSetters,
        bool $isUpdate = false,
        ConvertToArrayInterface $currentEntity = null
    ): ConvertToArrayInterface {
        $entity = $currentEntity ? $currentEntity : new $entityString();
        foreach ($entityBody as $columnKey => $columnValue) {
            $this->throwErrorIfNotValidSetter($columnSetters, $columnKey, $entityString, $entity);
            $entity->{$columnSetters[$columnKey]}($columnValue);
            unset($columnSetters[$columnKey]);
        }

        if (!$isUpdate && !empty($columnSetters)) {
            throw new RequestException(
                sprintf(
                    'Body of request does not contain the following values %s',
                    implode(',', array_keys($columnSetters))
                ),
                StatusCodes::UNPROCESSABLE_ENTITY
            );
        }

        return $entity;
    }

    /**
     * @param string[] $columnSetters
     * @param string $columnKey
     * @param string $entityString
     * @param ConvertToArrayInterface $entityObject
     * @throws RepositoryException|RequestException
     */
    private function throwErrorIfNotValidSetter(
        array $columnSetters,
        string $columnKey,
        string $entityString,
        ConvertToArrayInterface $entityObject
    ): void {
        if (empty($columnSetters[$columnKey])) {
            throw new RequestException(
                sprintf(
                    'Key name "%s" is not valid, the only valid key names are: %s',
                    $columnKey,
                    $this->repository->getColumnKeysAsString(true)
                ),
                StatusCodes::UNPROCESSABLE_ENTITY
            );
        }

        $method = $columnSetters[$columnKey];
        if (!method_exists($entityObject, $method)) {
            throw new RepositoryException(
                sprintf(
                    'Method "%s" does not exist on entity "%s". Check the set up for its repository.',
                    $method,
                    $entityString
                ),
                StatusCodes::NOT_IMPLEMENTED
            );
        }
    }
}
