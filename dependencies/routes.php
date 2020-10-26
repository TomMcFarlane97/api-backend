<?php

use App\Controller\UserController;
use App\Controller\NoteController;
use App\Controller\PingController;

$userRoute = '/user';
$specificUserRoute = $userRoute . '/{userId}';
$noteRoute = $specificUserRoute . '/note';
$specificNotesRoute = $noteRoute . '/{noteId}';

// ping
$app->any('/', PingController::class);

// User route
$app->get($userRoute, UserController::class . ':getAll');
$app->post($userRoute, UserController::class . ':createUser');
$app->get($specificUserRoute, UserController::class . ':getUser');
$app->patch($specificUserRoute, UserController::class . ':updateUser');
$app->delete($specificUserRoute, UserController::class . ':deleteUser');

// Note route
$app->get($noteRoute, NoteController::class . ':getAll');
$app->post($noteRoute, NoteController::class . ':createNote');
$app->get($specificNotesRoute, NoteController::class . ':getNote');
$app->patch($specificNotesRoute, NoteController::class . ':updateNote');
$app->delete($specificNotesRoute, NoteController::class . ':deleteNote');