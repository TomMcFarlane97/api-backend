<?php

namespace App\Traits;

use App\Controller\AbstractController;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Interfaces\ConvertToArrayInterface;

trait ValidSetterTrait
{
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
                    $this->userRepository->getColumnKeysAsString(true)
                ),
                AbstractController::UNPROCESSABLE_ENTITY
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
                AbstractController::NOT_IMPLEMENTED);
        }
    }
}