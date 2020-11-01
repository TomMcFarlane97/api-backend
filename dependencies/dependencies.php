<?php

use App\Interfaces\Repository\AbstractRepositoryInterface;
use App\Interfaces\Repository\NoteRepositoryInterface;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Repository\AbstractRepository;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

use function DI\autowire;

$logger = new Logger('logger');
$logger->pushHandler(new StreamHandler(
    dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'test_app.log',
    Logger::DEBUG
));
$logger->pushHandler(new FirePHPHandler());

return [
    'settings'  => [
        'determineRouteBeforeAppMiddleware' => true,
    ],
    'logger' => static function () use ($logger): LoggerInterface {
        return $logger;
    },
    LoggerInterface::class => $logger,
    AbstractRepositoryInterface::class => autowire(AbstractRepository::class),
    UserRepositoryInterface::class => autowire(UserRepository::class),
    NoteRepositoryInterface::class => autowire(NoteRepository::class),
];
