<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\JsonNoCacheResponse;
use DanackTest\BaseTestCase;
use Danack\Response\InvalidDataException;


class JsonNoCacheResponseTest extends BaseTestCase
{

    public function testWorksCorrectlyWithDefaults()
    {
        $data = ['foo' => 'bar'];
        $response = new JsonNoCacheResponse($data);
        $json = $response->getBody();
        $decodedData = json_decode($json, true);
        self::assertEquals($data, $decodedData);
        self::assertEquals(200, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(2, $setHeaders);
        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('application/json', $setHeaders['Content-Type']);

        self::assertArrayHasKey('Cache-Control', $setHeaders);
        self::assertEquals('no-cache, no-store', $setHeaders['Cache-Control']);
    }

    public function testWorksCorrectlyWithSettings()
    {
        $data = ['foo' => 'bar'];
        $headers = ['x-foo' => 'x-bar'];
        $response = new JsonNoCacheResponse($data, $headers, 420);
        $json = $response->getBody();
        $decodedData = json_decode($json, true);
        self::assertEquals($data, $decodedData);
        self::assertEquals(420, $response->getStatus());

        $setHeaders = $response->getHeaders();

        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('application/json', $setHeaders['Content-Type']);

        self::assertArrayHasKey('x-foo', $setHeaders);
        self::assertEquals('x-bar', $setHeaders['x-foo']);
    }

    // some data structures can't be converted to Json.
    // ensure that these generate the correct exception on construction.
    public function testDataNotTranformableToJson()
    {
        $a = new \StdClass;
        // Circular data structures can't be json encoded.
        $a->instance = $a;

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage("Recursion detected");
        new JsonNoCacheResponse($a);
    }
}
