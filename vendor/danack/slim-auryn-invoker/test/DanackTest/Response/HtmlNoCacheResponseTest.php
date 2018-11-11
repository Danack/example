<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\HtmlNoCacheResponse;
use DanackTest\BaseTestCase;

class HtmlNoCacheResponseTest extends BaseTestCase
{
    public function testWorksCorrectlyWithDefaults()
    {
        $html = "<head><body>Woot, some html.</body>/head>";
        $response = new HtmlNoCacheResponse($html);
        self::assertEquals($html,$response->getBody());
        self::assertEquals(200, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(2, $setHeaders);
        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/html', $setHeaders['Content-Type']);

        self::assertArrayHasKey('Cache-Control', $setHeaders);
        self::assertEquals('no-cache, no-store', $setHeaders['Cache-Control']);
    }

    public function testWorksCorrectlyWithSettings()
    {
        $headers = ['x-foo' => 'x-bar'];
        $html = "<head><body>Woot, some html.</body>/head>";
        $response = new HtmlNoCacheResponse($html, $headers);
        self::assertEquals($html,$response->getBody());

        $setHeaders = $response->getHeaders();
        self::assertCount(3, $setHeaders);

        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/html', $setHeaders['Content-Type']);

        self::assertArrayHasKey('Cache-Control', $setHeaders);
        self::assertEquals('no-cache, no-store', $setHeaders['Cache-Control']);

        self::assertArrayHasKey('x-foo', $setHeaders);
        self::assertEquals('x-bar', $setHeaders['x-foo']);
    }
}
