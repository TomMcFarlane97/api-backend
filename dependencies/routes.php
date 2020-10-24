<?php

use App\Controller\UserController;
use App\Controller\NoteController;
use Slim\Routing\RouteCollectorProxy;

$userRoute = '/user';
$specificUserRoute = $userRoute . '/{userId}';
$notesRoute = $specificUserRoute . '/notes';
$specificNotesRoute = $notesRoute . '/{noteId}';

$app->group($userRoute, function(RouteCollectorProxy $group) use ($specificUserRoute) {
    $group->get('', UserController::class . ':getAll');
    $group->post('', UserController::class . ':createUser');
    $group->get($specificUserRoute, UserController::class . ':getUser');
    $group->patch($specificUserRoute, UserController::class . ':updateUser');
    $group->delete($specificUserRoute, UserController::class . ':deleteUser');
});

$app->group($notesRoute, function(RouteCollectorProxy $group) use ($specificNotesRoute) {
    $group->get('', NoteController::class . ':getAll');
    $group->post('', NoteController::class . ':createNote');
    $group->get($specificNotesRoute, NoteController::class . ':getNote');
    $group->patch($specificNotesRoute, NoteController::class . ':updateNote');
    $group->delete($specificNotesRoute, NoteController::class . ':deleteNote');
});