<?php

use App\Controller\AuthenticationController;
use App\Controller\OptionsController;
use App\Controller\UserController;
use App\Controller\NoteController;
use App\Controller\PingController;

define('AUTHENTICATE_ROUTE', '/authenticate');
define('USER_ROUTE', '/user');
define('SPECIFIC_USER_ROUTE', '/user/{userId}');

$noteRoute = SPECIFIC_USER_ROUTE . '/note';
$specificNotesRoute = $noteRoute . '/{noteId}';

// Ping
$app->any('/', PingController::class);

// Authentication
$app->get(AUTHENTICATE_ROUTE, AuthenticationController::class . ':refresh');
$app->post(AUTHENTICATE_ROUTE, AuthenticationController::class . ':login');
$app->options(AUTHENTICATE_ROUTE, OptionsController::class);

// User route
$app->get(USER_ROUTE . 's', UserController::class . ':getAllAction');
$app->post(USER_ROUTE, UserController::class . ':createUserAction');
$app->get(USER_ROUTE, UserController::class . ':getUserAction');
$app->options(USER_ROUTE, OptionsController::class);
$app->patch(SPECIFIC_USER_ROUTE, UserController::class . ':updateUserAction');
$app->delete(SPECIFIC_USER_ROUTE, UserController::class . ':deleteUserAction');
$app->options(SPECIFIC_USER_ROUTE, OptionsController::class);

// Note route
$app->get($noteRoute, NoteController::class . ':getAll');
$app->post($noteRoute, NoteController::class . ':createNote');
$app->options($noteRoute, OptionsController::class);
$app->get($specificNotesRoute, NoteController::class . ':getNote');
$app->patch($specificNotesRoute, NoteController::class . ':updateNote');
$app->delete($specificNotesRoute, NoteController::class . ':deleteNote');
$app->options($specificNotesRoute, OptionsController::class);
