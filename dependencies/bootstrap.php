<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . 'dependencies.php');
$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'middleware.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true, $app->getContainer()->get('logger'));

$app->run();
