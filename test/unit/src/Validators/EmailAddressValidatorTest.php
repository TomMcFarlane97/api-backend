<?php

namespace App\Test\Validators;

use App\Exceptions\RequestException;
use App\Validators\EmailAddressValidator;
use Exception;
use PHPUnit\Framework\TestCase;

class EmailAddressValidatorTest extends TestCase
{
    /**
     * @covers \App\Validators\EmailAddressValidator
     */
    public function testEmailValidatorSuccess(): void
    {
        $trueEmail = 'test@test.com';
        try {
            $email = (new EmailAddressValidator($trueEmail))->getEmailAddress();
        } catch (Exception $exception) {
            $this->fail(sprintf('method "%s" is failing with an invalid email address', __METHOD__));
            return;
        }

        $this->assertEquals($trueEmail, $email);
    }

    /**
     * @covers \App\Validators\EmailAddressValidator
     */
    public function testEmailValidatorFails(): void
    {
        $trueEmail = 'invalid email address';
        try {
            $email = (new EmailAddressValidator($trueEmail))->getEmailAddress();
        } catch (Exception $exception) {
            $this->assertInstanceOf(RequestException::class, $exception);
            return;
        }

        $this->fail(sprintf('method "%s" is failing with an correct email address of "%s"', __METHOD__, $email));
    }
}