<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\NotFoundResponse;
use DanackTest\BaseTestCase;
use Danack\Response\InvalidDataException;


class NotFoundResponseTest extends BaseTestCase
{
    public function testWorksCorrectlyWithDefaults()
    {
        $message = 'Content not found';

        $response = new NotFoundResponse($message);
        self::assertEquals($message, $response->getBody());
        self::assertCount(0, $response->getHeaders());
        self::assertEquals(404, $response->getStatus());
    }
}
