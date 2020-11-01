<?php

use App\Controller\AuthenticationController;
use App\Controller\OptionsController;
use App\Controller\UserController;
use App\Controller\NoteController;
use App\Controller\PingController;

define('AUTHENTICATE_ROUTE', '/authenticate');
define('USER_ROUTE', '/user');
define('SPECIFIC_USER_ROUTE', '/user/{userId}');

$noteRoute = $specificUserRoute . '/note';
$specificNotesRoute = $noteRoute . '/{noteId}';

// Set preflight options request
$app->options('/{routes:.+}', OptionsController::class);

// Ping
$app->any('/', PingController::class);

// Authentication
$app->get(AUTHENTICATE_ROUTE, AuthenticationController::class . ':refresh');
$app->post(AUTHENTICATE_ROUTE, AuthenticationController::class . ':login');

// User route
$app->get(USER_ROUTE, UserController::class . ':getAll');
$app->post(USER_ROUTE, UserController::class . ':createUser');
$app->get(SPECIFIC_USER_ROUTE, UserController::class . ':getUser');
$app->patch(SPECIFIC_USER_ROUTE, UserController::class . ':updateUser');
$app->delete(SPECIFIC_USER_ROUTE, UserController::class . ':deleteUser');

// Note route
$app->get($noteRoute, NoteController::class . ':getAll');
$app->post($noteRoute, NoteController::class . ':createNote');
$app->get($specificNotesRoute, NoteController::class . ':getNote');
$app->patch($specificNotesRoute, NoteController::class . ':updateNote');
$app->delete($specificNotesRoute, NoteController::class . ':deleteNote');
