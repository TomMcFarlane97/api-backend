<?php

namespace App\Helpers;

abstract class StatusCodes
{
    public const ACCEPTED = 200;
    public const BAD_REQUEST = 400;
    public const UNSUPPORTED_MIME_TYPE = 415;
    public const TEA_POT = 418;
    public const UNPROCESSABLE_ENTITY = 422;
    public const CREATED = 201;
    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
}
