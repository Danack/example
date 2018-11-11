<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\RedirectResponse;
use DanackTest\BaseTestCase;

class RedirectResponseTest extends BaseTestCase
{
    public function testWorksCorrectlyWithDefaults()
    {
        $newLocation = "https://www.google.com/";
        $response = new RedirectResponse($newLocation);
        self::assertEmpty($response->getBody());
        self::assertEquals(302, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(1, $setHeaders);
        self::assertArrayHasKey('Location', $setHeaders);
        self::assertEquals($newLocation, $setHeaders['Location']);
    }

    public function testWorksCorrectlyWithSettings()
    {
        $headers = ['x-foo' => 'x-bar'];
        $newLocation = "https://www.google.com/";
        $response = new RedirectResponse($newLocation, 307, $headers);
        self::assertEmpty($response->getBody());
        self::assertEquals(307, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(2, $setHeaders);

        self::assertArrayHasKey('Location', $setHeaders);
        self::assertEquals($newLocation, $setHeaders['Location']);

        self::assertArrayHasKey('x-foo', $setHeaders);
        self::assertEquals('x-bar', $setHeaders['x-foo']);
    }
}
