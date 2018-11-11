<?php

declare(strict_types=1);

namespace Example\Route;

use FastRoute\RouteCollector;

interface Routes
{
    public function getRoutes();
    public function addRoutesToCollector(RouteCollector $routeCollector);
}
