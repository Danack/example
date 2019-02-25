<?php

namespace SlimAurynTest;

use Auryn\Injector;
use SlimAuryn\Response\TextResponse;
use SlimAuryn\RouteMiddlewares;
use SlimAuryn\SlimAurynInvoker;
use SlimAuryn\SlimAurynException;
use SlimAurynTest\BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use SlimAuryn\RouteParams;
use Slim\Route;

class SlimAurynInvokerTest extends BaseTestCase
{
    /**
     * Test when a callable returns a stub response, all is well.
     */
    public function testStubResponse()
    {
        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();
        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );

        $requestReceived = null;
        $callable = function (ServerRequestInterface $request) use (&$requestReceived) {
            // This response will have a 200 status
            $requestReceived = $request;
            return new TextResponse("This is a response", [], 420);
        };

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();
        $returnedResponse = $invoker(
            $callable,
            $requestMock,
            $response,
            []
        );

        // Check that the request object reached the callable.
        self::assertSame($requestMock, $requestReceived);

        // Check that the response will passed into the PSR7 response correctly
        self::assertEquals(420, $returnedResponse->getStatusCode());
        self::assertEquals(true, $returnedResponse->hasHeader('Content-Type'));
        self::assertEquals(
            ['text/plain'],
            $returnedResponse->getHeader('Content-Type')
        );
    }

    /**
     * Test that when a callable returns a PSR 7 response, all is well.
     */
    public function testPsr7Response()
    {
        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();
        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );

        $requestReceived = null;
        $callable = function (ResponseInterface $response) {

            $response = $response->withStatus(420);
            /** @var $response \Psr\Http\Message\ResponseInterface */
            $response = $response->withHeader('Content-Type', 'text/awesome');

            return $response;
        };

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $response = new Response();
        $returnedResponse = $invoker(
            $callable,
            $requestMock,
            $response,
            []
        );

        self::assertEquals(420, $returnedResponse->getStatusCode());
        self::assertEquals(true, $returnedResponse->hasHeader('Content-Type'));
        self::assertEquals(
            ['text/awesome'],
            $returnedResponse->getHeader('Content-Type')
        );
    }

    /**
     * Test that a callable returns null unexpectedly
     * throws an exception.
     */
    public function testBadCallableReturningNull()
    {
        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();

        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );

        $callable = function () {
        };

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $this->expectException(SlimAurynException::class);
        $this->expectExceptionMessage("returned [NULL]");
        $invoker(
            $callable,
            $requestMock,
            $response,
            []
        );
    }


    /**
     * Test that a callable that returns an unknown object type
     * throws an exception.
     */
    public function testBadCallableReturningObject()
    {
        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();
        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );

        $callable = function () {
            return new \StdClass();
        };


        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $this->expectException(SlimAurynException::class);
        $this->expectExceptionMessage("object of type stdClass");

        $invoker(
            $callable,
            $requestMock,
            $response,
            []
        );
    }

    /**
     * Test that the correct mappers are used when the callable returns
     * each of those types.
     */
    public function testMapperIsUsed()
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
        $routeMiddlewares = new RouteMiddlewares();
        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            $resultMappers
        );

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


    public function testRouteParamsAvailable()
    {
        $routeParamsArray = null;

        $checkRouteParamsFn = function (RouteParams $routeParams) use (&$routeParamsArray) {
            $routeParamsArray = $routeParams->getAll();

            return 'hello world';
        };

        $request = createRequestForTesting();
        $response = new Response();

        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();

        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );
        $routeArgs = [
            'foo' => 'bar'
        ];


        $invoker->__invoke(
            $checkRouteParamsFn,
            $request,
            $response,
            $routeArgs
        );

        $this->assertSame($routeArgs, $routeParamsArray);
    }


    public function testSetupCallableCalledCorrectly()
    {
        $checkRouteParamsFn = function () {
            return 'hello world';
        };

        $setupCallableCalled = false;

        $setupFn = function () use (&$setupCallableCalled) {
            $setupCallableCalled = true;
        };

        $request = createRequestForTesting();
        $response = new Response();

        $injector = new Injector();
        $routeMiddlewares = new RouteMiddlewares();

        $invoker = new SlimAurynInvoker(
            $injector,
            $routeMiddlewares,
            getResultMappers()
        );

        $route = new Route(
            $methods = ['GET'],
            $pattern = '/',
            $callable = 'not_used'
        );

        $route->setArgument('setupCallable', $setupFn);

        $request = $request->withAttributes(['route' => $route]);
        $invoker->__invoke(
            $checkRouteParamsFn,
            $request,
            $response,
            []
        );

        $this->assertTrue($setupCallableCalled);
    }
}
