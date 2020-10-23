<?php

namespace App\Interfaces;

use App\Exceptions\EntityException;

interface ConvertToArrayInterface
{
    /**
     * @return array<string, mixed>
     * @throws EntityException
     */
    public function convertToArray(): array;
}