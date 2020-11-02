<?php

use App\Middleware\CORSMiddleware;

$app->add(CORSMiddleware::class);
//$app->add(AuthenticationMiddleware::class);
