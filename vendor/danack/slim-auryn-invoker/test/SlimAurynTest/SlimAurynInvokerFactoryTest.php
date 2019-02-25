<?php

namespace SlimAurynTest;

use Auryn\Injector;
use SlimAuryn\RouteMiddlewares;
use Pimple\Container;
use SlimAuryn\SlimAurynInvoker;
use SlimAuryn\SlimAurynInvokerFactory;
use SlimAurynTest\BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;


class SlimAurynInvokerFactoryTest extends BaseTestCase
{
    public function testCreate()
    {
        $injector = new Injector();
        $container = new Container();
        $routeMiddlewares = new RouteMiddlewares();
        $invokerFactory = new SlimAurynInvokerFactory(
            $injector,
            $routeMiddlewares,
            []
        );
        $invoker = $invokerFactory($container);
        $this->assertInstanceOf(SlimAurynInvoker::class, $invoker);
    }

    /**
     * Test that the correct mappers are used when the callable returns
     * each of those types, when created through the factory.
     */

    public function testCreateAndMappers()
    {
        $stringMapperUsed = false;
        $stringToResponseMapper = function(string $value, ResponseInterface $response) use (&$stringMapperUsed) {
            $response = $response->withStatus(420);
            $stringMapperUsed = true;
            return $response;
        };

        $stdClassMapperUsed = false;
        $stdClassResponseMapper = function(\StdClass $stdClass, ResponseInterface $response) use (&$stdClassMapperUsed) {
            $response = $response->withStatus(420);
            $stdClassMapperUsed = true;
            return $response;
        };

        $resultMappers = [
            'string' => $stringToResponseMapper,
            \StdClass::class => $stdClassResponseMapper,
        ];

        $injector = new Injector();
        $container = new Container();
        $routeMiddlewares = new RouteMiddlewares();

        $invokerFactory = new SlimAurynInvokerFactory(
            $injector,
            $routeMiddlewares,
            $resultMappers
        );

        $invoker = $invokerFactory($container);
        $returnsAString = function () {
            return "This could be some html.";
        };

        $returnsAStdClass = function () {
            return new \StdClass();
        };

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();
        $response = $invoker->__invoke($returnsAString, $requestMock, $response, []);
        self::assertTrue($stringMapperUsed);
        self::assertFalse($stdClassMapperUsed);
        self::assertInstanceOf(ResponseInterface::class, $response);


        // reset to test StdClass mapper is used correct
        $stringMapperUsed = false;
        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();
        $response = $invoker->__invoke($returnsAStdClass, $requestMock, $response, []);
        self::assertFalse($stringMapperUsed);
        self::assertTrue($stdClassMapperUsed);
        self::assertInstanceOf(ResponseInterface::class, $response);
    }
}
