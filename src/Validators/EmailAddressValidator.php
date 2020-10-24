<?php

namespace App\Validators;

use App\Controller\AbstractController;
use App\Exceptions\RequestException;

class EmailAddressValidator
{
    private string $emailAddress;

    /**
     * @param string $emailAddress
     * @throws RequestException
     */
    public function __construct(string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new RequestException(
                sprintf('Email of "%s" is not valid.', $emailAddress),
                AbstractController::UNPROCESSABLE_ENTITY
            );
        }
        $emailAddressSanitised = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);
        if (!$emailAddressSanitised) {
            throw new RequestException(
                sprintf('Email of "%s" is valid but cannot can not be sanitised.', $emailAddress),
                AbstractController::UNPROCESSABLE_ENTITY
            );
        }
        $this->emailAddress = $emailAddressSanitised;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }
}