<?php

declare(strict_types=1);

namespace SlimAurynTest\ResponseMapper;

use Slim\Http\Response;
use SlimAurynTest\BaseTestCase;
use SlimAuryn\Response\TextResponse;
use SlimAuryn\ResponseMapper\ResponseMapper;

/**
 * @coversNothing
 */
class ResponseMapperTest extends BaseTestCase
{
    /**
     * @covers \SlimAuryn\ResponseMapper\ResponseMapper::mapStubResponseToPsr7
     */
    public function testMapStubResponseToPsr7()
    {
        $originalResponse = new Response();

        $text = 'This is some text';
        $headers = [
            'foo' => 'bar'
        ];
        $status = 201;

        $textResponse = new TextResponse($text, $headers, $status);

        $responseReturned = ResponseMapper::mapStubResponseToPsr7(
            $textResponse,
            $originalResponse
        );

        $this->assertSame($status, $responseReturned->getStatusCode());
        $responseReturned->getBody()->rewind();
        $this->assertSame($text, $responseReturned->getBody()->getContents());

        $this->assertTrue($responseReturned->hasHeader('foo'));
        $this->assertSame('bar', $responseReturned->getHeaderLine('foo'));
    }


    /**
     * @covers \SlimAuryn\ResponseMapper\ResponseMapper::passThroughResponse
     */
    public function testPassThrough()
    {
        $originalResponse = new Response();
        $controllerResponse = new Response();

        $responseReturned = ResponseMapper::passThroughResponse(
            $controllerResponse,
            $originalResponse
        );

        $this->assertSame($controllerResponse, $responseReturned);
    }
}
