<?php

namespace DanackTest\SlimAurynInvoker;

use Auryn\Injector;
use Danack\Response\TextResponse;
use Danack\SlimAurynInvoker\SlimAurynInvoker;
use Danack\SlimAurynInvoker\SlimAurynInvokerException;
use DanackTest\BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class SlimAurynInvokerTest extends BaseTestCase
{
    /**
     * Test when a callable returns a stub response, all is well.
     */
    public function testStubResponse()
    {
        $injector = new Injector();
        $invoker = new SlimAurynInvoker($injector);

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
        $invoker = new SlimAurynInvoker($injector);

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
        $invoker = new SlimAurynInvoker($injector);

        $callable = function () {};

        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $this->expectException(SlimAurynInvokerException::class);
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
        $invoker = new SlimAurynInvoker($injector);

        $callable = function () {
            return new \StdClass();
        };


        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();


        $this->expectException(SlimAurynInvokerException::class);
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
        $invoker = new SlimAurynInvoker($injector, $resultMappers);

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
        $returnedResponse = $invoker->__invoke($returnsAStdClass, $requestMock, $response, []);
        self::assertFalse($stringMapperUsed);
        self::assertTrue($stdClassMapperUsed);
        self::assertInstanceOf(ResponseInterface::class, $returnedResponse);
    }

    /**
     * Test that a callable that throws a specific exception type, has
     * the specific exception handler called
     */
    public function testExceptionCatchingCallsCorrectCallable()
    {
        $stringMapperUsed = false;
        $stringToResponseMapper = function(string $value, ResponseInterface $response) use (&$stringMapperUsed) {
            $response = $response->withStatus(123);
            $stringMapperUsed = true;
            return $response;
        };

        $exceptionToStringCalled = false;
        $testExceptionToStringCallable = function (\DanackTest\TestException $te) use (&$exceptionToStringCalled) {
            $exceptionToStringCalled = true;
            return $te->getMessage();
        };

        $injector = new Injector();
        $invoker = new SlimAurynInvoker(
            $injector,
            ['string' => $stringToResponseMapper,],
            null,
            [\DanackTest\TestException::class => $testExceptionToStringCallable]
        );

        $testString = 'testing correct handler called';

        $callable = function () use ($testString) {
            throw new \DanackTest\TestException($testString);
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

        self::assertTrue($stringMapperUsed);
        self::assertTrue($exceptionToStringCalled);
        self::assertInstanceOf(ResponseInterface::class, $returnedResponse);
        self::assertEquals(123, $returnedResponse->getStatusCode());
    }


    /**
     * Test that a callable that throws a generic exception type, doesn't
     * have an inappropriate exception handler called
     */
    public function testExceptionCatchingFallback()
    {

        $exceptionToStringCalled = false;
        $testExceptionToStringCallable = function (\DanackTest\TestException $te) use (&$exceptionToStringCalled) {
            $exceptionToStringCalled = true;
            return $te->getMessage();
        };

        $injector = new Injector();
        $invoker = new SlimAurynInvoker(
            $injector,
            [],
            null,
            [\DanackTest\TestException::class => $testExceptionToStringCallable]
        );

        $testString = 'testing correct handler called';

        $callable = function () use ($testString) {
            throw new \Exception($testString);
        };


        /** @var $requestMock \Psr\Http\Message\ServerRequestInterface */
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        try {
            $invoker(
                $callable,
                $requestMock,
                $response,
                []
            );
            $this->fail("This code is not meant to be reached");
        }
        catch (\Exception $e) {
            $this->assertEquals($testString, $e->getMessage());
        }

        self::assertFalse($exceptionToStringCalled);

    }

}
