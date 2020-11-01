<?php

namespace App\Validators;

use App\Exceptions\RequestException;
use App\Helpers\StatusCodes;

class IntegerIDValidator
{
    private int $id;

    /**
     * IntegerIDValidator constructor.
     * @param int|mixed $id
     * @throws RequestException
     */
    public function __construct($id)
    {
        $this->id = $this->validateInteger($id);
    }

    /**
     * @param int|mixed $id
     * @return int
     * @throws RequestException
     */
    private function validateInteger($id): int
    {
        if (!filter_var($id, FILTER_VALIDATE_INT) && $id !== 0) {
            throw new RequestException(sprintf('This value "%s" should be an integer', $id), StatusCodes::BAD_REQUEST);
        }
        return (int) $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
