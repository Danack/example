<?php

declare(strict_types=1);

namespace SlimAuryn;

use Slim\App;

class Routes
{
    private $routes;

    public function __construct($routesFilename)
    {
        // Each row of this array should return an array of:
        // - The path to match
        // - The method to match
        // - The route info
        // - (optional) A setup callable to add middleware/DI info specific to that route
        //
        // This allows use to configure data per endpoint e.g. the endpoints that should be secured by
        // an api key, should call an appropriate callable.
        $this->routes = require $routesFilename;
    }

    public function setupRoutes(App $app)
    {
        foreach ($this->routes as $route) {
            list($path, $method, $callable) = $route;
            $slimRoute = $app->map([$method], $path, $callable);

            if (array_key_exists(3, $route) === true) {
                $setupCallable = $route[3];
                $slimRoute->setArgument(SlimAurynInvoker::SETUP_ARGUMENT_NAME, $setupCallable);
            }
        }
    }
}
