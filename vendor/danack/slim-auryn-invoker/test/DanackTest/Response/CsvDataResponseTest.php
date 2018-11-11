<?php

namespace DanackTest\SlimAurynInvoker;

use Danack\Response\CsvDataResponse;
use DanackTest\BaseTestCase;
use Danack\Response\InvalidDataException;


class CsvDataResponseTest extends BaseTestCase
{
    private static $testData = [
        ['Zoq', 'Fot', 'Pik', 'Zebranky'],
    ];

    public function testWorksCorrectlyWithDefaults()
    {
        $response = new CsvDataResponse(self::$testData);

        $expectedBody = "Zoq,Fot,Pik,Zebranky
";

        self::assertEquals($expectedBody, $response->getBody());
        self::assertEquals(200, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(2, $setHeaders);
        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/csv', $setHeaders['Content-Type']);

        self::assertArrayHasKey('Content-Disposition', $setHeaders);
        self::assertEquals('attachment; filename=file.csv', $setHeaders['Content-Disposition']);
    }

    public function testWorksCorrectlyWithSettings()
    {
        $filename = 'test.csv';
        $status = 420;
        $dataHeaders = ['food', 'food', 'food', 'predator'];

        $headers = ['x-foo' => 'x-bar'];
        $response = new CsvDataResponse(self::$testData, $dataHeaders, $filename, $headers, $status);

        $expectedBody = "food,food,food,predator\nZoq,Fot,Pik,Zebranky\n";

        self::assertEquals($expectedBody, $response->getBody());
        self::assertEquals($status, $response->getStatus());

        $setHeaders = $response->getHeaders();
        self::assertCount(3, $setHeaders);
        self::assertArrayHasKey('Content-Type', $setHeaders);
        self::assertEquals('text/csv', $setHeaders['Content-Type']);

        self::assertArrayHasKey('Content-Disposition', $setHeaders);
        self::assertEquals('attachment; filename='.$filename, $setHeaders['Content-Disposition']);

        self::assertArrayHasKey('x-foo', $setHeaders);
        self::assertEquals('x-bar', $setHeaders['x-foo']);
    }

    // TODO - check that data is transformable to CSV
//    public function testDataNotTranformableToCsv()
//    {
//
//    }
}
