<?php

namespace App\Middleware;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class CORSMiddleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->logger->error('I am in ' . __CLASS__ . '::' . __METHOD__);
        if ($request->getMethod() !== 'OPTIONS' || php_sapi_name() === 'cli') {
            return $handler->handle($request);
        }

        $route = $request->getAttribute('route');
        $methods = [];

        if (!empty($route)) {
            $pattern = $route->getPattern();

            foreach ($this->router->getRoutes() as $route) {
                if ($pattern === $route->getPattern()) {
                    $methods = array_merge_recursive($methods, $route->getMethods());
                }
            }
        } else {
            $methods[] = $request->getMethod();
        }

        return $handler->handle($request)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin')
            ->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    }
}
