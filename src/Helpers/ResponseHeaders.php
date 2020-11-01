<?php

namespace App\Helpers;

abstract class ResponseHeaders
{
    public const HEADER_AUTHORIZATION = 'Authorization';
    protected const JSON = 'application/json';
    protected const HEADER_CONTENT_TYPE = 'Content-type';
    protected const HEADER_ACCEPT = 'Accept';
}
