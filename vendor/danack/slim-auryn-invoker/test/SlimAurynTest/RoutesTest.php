<?php

declare(strict_types=1);

namespace SlimAurynTest;

use Mockery;
use SlimAuryn\Routes;
use SlimAuryn\SlimAurynInvoker;

class RoutesTest extends BaseTestCase
{
    public function testBasic()
    {
        $routes = new Routes(__DIR__ . '/../test_routes.php');

        $route1 = Mockery::mock('Slim\Interfaces\RouteInterface');
        $route2 = Mockery::mock('Slim\Interfaces\RouteInterface');

        $route2
          ->shouldReceive('setArgument')
          ->withArgs([SlimAurynInvoker::SETUP_ARGUMENT_NAME, 'setup_callable']);

        $app = Mockery::mock('Slim\App');
        $app->shouldReceive('map')->withArgs([['GET'], '/foo', 'callable_1'])->andReturn($route1);
        $app->shouldReceive('map')->withArgs([['GET'], '/bar', 'callable_2'])->andReturn($route2);

        $routes->setupRoutes($app);

        \Mockery::close();
    }
}
