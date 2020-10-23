<?php

use App\Controller\UserController;
use Slim\Routing\RouteCollectorProxy;

$app->group('/user', function(RouteCollectorProxy $group) {
    $group->get('', UserController::class . ':getAll');
    $group->post('', UserController::class . ':createUser');
    $group->get('/{userId}', UserController::class . ':getUser');
    $group->patch('/{userId}', UserController::class . ':updateUser');
    $group->delete('/{userId}', UserController::class . ':deleteUser');
});