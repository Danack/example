<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\HtmlResponse;
use DanackTest\BaseTestCase;

class HtmlResponseTest extends BaseTestCase
{
    public function testWorksCorrectlyWithDefaults()
    {
        $html = "<head><body>Woot, some html.</body>/head>";
        $response = new HtmlResponse($html);
        self::assertEquals($html,$response->getBody());
        self::assertEquals(200, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(1, $setHeaders);
        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/html', $setHeaders['Content-Type']);
    }

    public function testWorksCorrectlyWithSettings()
    {
        $headers = ['x-foo' => 'x-bar'];
        $html = "<head><body>Woot, some html.</body>/head>";
        $response = new HtmlResponse($html, $headers);
        self::assertEquals($html,$response->getBody());

        $setHeaders = $response->getHeaders();
        self::assertCount(2, $setHeaders);

        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/html', $setHeaders['Content-Type']);

        self::assertArrayHasKey('x-foo', $setHeaders);
        self::assertEquals('x-bar', $setHeaders['x-foo']);
    }
}
