<?php

namespace SlimAurynTest;

use Auryn\Injector;
use SlimAuryn\SlimAurynInvoker;

use SlimAuryn\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SlimAuryn\Response\StubResponse;
use SlimAuryn\RouteParams;
use SlimAurynTest\BaseTestCase;


class SlimAurynInvoker_SetInjectorInfoTest extends BaseTestCase
{
    public function testSetInjectorInfo()
    {
        $injector = new Injector();

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        /** @var $responseMock \Psr\Http\Message\ResponseInterface */
        $responseMock = $this->createMock(ResponseInterface::class);
        $routeArguments = ['foo' => 'bar'];

        Util::setInjectorInfo(
            $injector,
            $requestMock,
            $responseMock,
            $routeArguments
        );

        // Check that the request and response objects are available.
        $builtRequest = $injector->make(ServerRequestInterface::class);
        self::assertSame($requestMock, $builtRequest);

        $builtResponse = $injector->make(ResponseInterface::class);
        self::assertSame($responseMock, $builtResponse);

        // Check that the param is available by name
        $fn = function ($foo) {
            return $foo;
        };
        $shouldBeBar = $injector->execute($fn);
        self::assertEquals('bar', $shouldBeBar);

        // Check that the route params contains the route variable.
        $routeParams = $injector->make(RouteParams::class);
        self::assertTrue($routeParams->hasValue('foo'));
        self::assertEquals('bar', $routeParams->getValue('foo'));
    }
}
