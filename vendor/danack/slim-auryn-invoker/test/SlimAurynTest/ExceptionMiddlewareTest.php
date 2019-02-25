<?php

declare(strict_types=1);

namespace SlimAurynTest;

use SlimAurynTest\BaseTestCase;
use SlimAuryn\ExceptionMiddleware;
use Psr\Http\Message\ResponseInterface;
use SlimAurynTest\MappedException;
use SlimAurynTest\UnmappedException;
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Body;
use Slim\Http\Uri;
use Slim\Http\Headers;

class ExceptionMiddlewareTest extends BaseTestCase
{
    public function testCallableCalledProperly()
    {
        $nextFn = function (Request $request, ResponseInterface $response) {
            return 'Test output';
        };

        $responseString = $this->performRequest(
            $nextFn,
            [],
            []
        );


        $this->assertEquals('Test output', $responseString);
    }


    public function testParseErrorMappedProperly()
    {
        $nextFn = function (Request $request, ResponseInterface $response) {
            return eval('return $x + 4a');
        };

        $message = null;

        $handleException = function (\ParseError $exception, ResponseInterface $response) use (&$message) {
            $message = $exception->getMessage();

            $response = $response->withStatus(503);

            return $response;
        };

        $response = $this->performRequest(
            $nextFn,
            [],
            [\ParseError::class => $handleException]
        );

        $this->assertSame(
            "syntax error, unexpected 'a' (T_STRING), expecting ';'",
            $message
        );
        $this->assertSame(503, $response->getStatusCode());
    }



    public function testExceptionResultConvertedToResponse()
    {

        $nextFn = function (Request $request, ResponseInterface $response) {
            throw new MappedException("This is a mapped exception.");
        };

        $convertStringToHtmlResponseFn = function (string $result, ResponseInterface $response) {
            $response = $response->withHeader('Content-Type', 'text/html');
            $response->getBody()->write($result . " That was mapped to html");
            return $response;
        };

        $resultMappers = [
            'string' => $convertStringToHtmlResponseFn
        ];

        $exceptionMappers = [
            MappedException::class => function (MappedException $mappedException) {
                return $mappedException->getMessage();
            }
        ];

        $response = $this->performRequest(
            $nextFn,
            $resultMappers,
            $exceptionMappers
        );

        $response->getBody()->rewind();
        $contents = $response->getBody()->getContents();

        $this->assertEquals(
            "This is a mapped exception." . " That was mapped to html",
            $contents
        );
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('text/html', $response->getHeaderLine('Content-Type'));
    }


    public function testExceptionUnmappedEscapes()
    {
        $nextFn = function (Request $request, ResponseInterface $response) {
            throw new UnmappedException("This is an unmapped exception.");
        };

        $convertStringToHtmlResponseFn = function (string $result, ResponseInterface $response) {
            $response = $response->withHeader('Content-Type', 'text/html');
            $response->getBody()->write($result . " That was mapped to html");
            return $response;
        };

        $resultMappers = [
            'string' => $convertStringToHtmlResponseFn
        ];

        $exceptionMappers = [
            MappedException::class => function (MappedException $mappedException) {
                return $mappedException->getMessage();
            }
        ];

        $this->expectException(UnmappedException::class);
        $this->expectExceptionMessage("This is an unmapped exception.");
        $this->performRequest(
            $nextFn,
            $resultMappers,
            $exceptionMappers
        );
    }

    public function performRequest($nextFn, $resultMappers, $exceptionHandlers)
    {
        $response = new Response();

        $headers = [];
        $bodyContent = '';
        $cookies = [];

        $uri = Uri::createFromString('https://user:pass@host:443/path?query');
        $headers = new Headers($headers);
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($bodyContent);
        $body->rewind();
        $method = 'GET';
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        $exceptionMiddleware = new ExceptionMiddleware(
            $exceptionHandlers,
            $resultMappers
        );

        return $exceptionMiddleware->__invoke($request, $response, $nextFn);
    }
}
