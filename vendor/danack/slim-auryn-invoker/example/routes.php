<?php

use Slim\App;

function setupBasicRoutes(App $app)
{
    $routes = [
        ['/', 'GET', 'Danack\SlimAurynExample\ResponseController::getHomePage'],
    ];

    foreach ($routes as $route) {
        list($path, $method, $callable) = $route;
        $app->{$method}($path, $callable);
    }
}



function setupHtmlRoutes(App $app)
{
    $routes = [
        ['/', 'GET', 'Danack\SlimAurynExample\HtmlController::getPage'],
    ];

    foreach ($routes as $route) {
        list($path, $method, $callable) = $route;
        $app->{$method}($path, $callable);
    }
}