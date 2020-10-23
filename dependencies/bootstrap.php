<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ .DIRECTORY_SEPARATOR . 'dependencies.php');
$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

$app->run();