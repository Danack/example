<?php

declare(strict_types=1);

use Auryn\Injector;

function createSlimAurynInvokerFactory(
    Injector $injector,
    \SlimAuryn\RouteMiddlewares $routeMiddlewares
) {
    $resultMappers = getResultMappers();

    return new SlimAuryn\SlimAurynInvokerFactory(
        $injector,
        $routeMiddlewares,
        $resultMappers
    );
}

function createExceptionMiddleware()
{
    return new SlimAuryn\ExceptionMiddleware(
        getExceptionMappers(),
        getResultMappers()
    );
}

function createFoo() : \SlimAurynTest\Foo\Foo
{
    return new \SlimAurynTest\Foo\StandardFoo(true);
}
