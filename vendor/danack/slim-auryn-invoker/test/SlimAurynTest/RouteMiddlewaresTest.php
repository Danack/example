<?php

declare(strict_types=1);

namespace SlimAurynTest;

use SlimAurynExample\NullMiddleware;
use SlimAurynTest\BaseTestCase;
use SlimAuryn\RouteMiddlewares;
use Slim\Http\Response;
use UnexpectedValueException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class RouteMiddlewaresTest extends BaseTestCase
{
    public function testBadReturnGivesException()
    {
        $routeMiddlewares = new RouteMiddlewares();

        $request = createRequestForTesting();
        $response = new Response();

        $fn = function (ServerRequestInterface $request, ResponseInterface $response) {
            return 'hello world';
        };

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Middleware must return instance of \Psr\Http\Message\ResponseInterface');

        $routeMiddlewares->execute($fn, $request, $response);
    }


    public function testBadReturnGivesExceptionWithMiddleware()
    {
        $routeMiddlewares = new RouteMiddlewares();
        $routeMiddlewares->addMiddleware(new NullMiddleware());

        $request = createRequestForTesting();
        $response = new Response();

        $fn = function (ServerRequestInterface $request, ResponseInterface $response) {
            return 'hello world';
        };

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Middleware must return instance of \Psr\Http\Message\ResponseInterface');

        $routeMiddlewares->execute($fn, $request, $response);
    }


    public function testMiddlewareCalled()
    {
        $routeMiddlewares = new RouteMiddlewares();

        $middleware = new NullMiddleware();
        $routeMiddlewares->addMiddleware($middleware);

        $request = createRequestForTesting();
        $response = new Response();
        $callableWasCalled = false;

        $fn = function (ServerRequestInterface $request, ResponseInterface $response) use (&$callableWasCalled) {
            $callableWasCalled = true;
            return $response;
        };

        $routeMiddlewares->execute($fn, $request, $response);

        $this->assertTrue($middleware->wasCalled());
        $this->assertTrue($callableWasCalled);
    }
}
