<?php

namespace App\Traits;

use App\Exceptions\EntityException;
use App\Helpers\StatusCodes;

/**
 * Trait ConvertToArrayTrait
 * @package App\Traits
 * @property-read array<string, string> $columnGetters
 */
trait ConvertToArrayTrait
{
    /**
     * @return array<string, mixed>
     * @throws EntityException
     */
    public function convertToArray(): array
    {
        $transformer = [];
        foreach (array_keys($this->columnGetters) as $key) {
            if (!isset($this->{$key})) {
                throw new EntityException(
                    sprintf('"%s" Entity does not contain this column in the trait "%s"', __CLASS__, $key),
                    StatusCodes::TEA_POT
                );
            }
            $transformer[$key] = $this->{$key};
        }
        return $transformer;
    }
}
