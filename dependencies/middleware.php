<?php

use App\Middleware\CORSMiddleware;
use App\Middleware\AuthenticationMiddleware;

$app->add(CORSMiddleware::class);
$app->add(AuthenticationMiddleware::class);
