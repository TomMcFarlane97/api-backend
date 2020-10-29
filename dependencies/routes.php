<?php

use App\Controller\OptionsController;
use App\Controller\UserController;
use App\Controller\NoteController;
use App\Controller\PingController;

$userRoute = '/user';
$specificUserRoute = $userRoute . '/{userId}';
$noteRoute = $specificUserRoute . '/note';
$specificNotesRoute = $noteRoute . '/{noteId}';

// Set preflight options request
$app->options('/{routes:.+}', OptionsController::class);

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

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
