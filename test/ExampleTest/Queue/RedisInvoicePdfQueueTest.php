<?php

declare(strict_types=1);

namespace ExampleTest\Queue;

use Example\Queue\PrintUrlToPdfJob;
use ExampleTest\BaseTestCase;
use Example\Queue\RedisPrintUrlToPdfQueue;

class RedisInvoicePdfQueueTest extends BaseTestCase
{
    public function testPushingRequestsToQueue()
    {
        $redis = createRedis();

        $pdfQueue = new RedisPrintUrlToPdfQueue($redis);

        $pdfQueue->clearQueue();

        $existingPdfJob = $pdfQueue->getPrintUrlToPdfJob(1);
        $this->assertNull($existingPdfJob);

        $url = 'http://www.google.com/';
        $filename = 'foobar.txt';

        $invoicePdfJob = new PrintUrlToPdfJob($url, $filename);
        $pdfQueue->pushPrintUrlToPdfJob($invoicePdfJob);

        $printUrlToPdfJob = $pdfQueue->getPrintUrlToPdfJob(5);

        $this->assertEquals($url, $printUrlToPdfJob->getUrl());
        $this->assertEquals($filename, $printUrlToPdfJob->getFilename());

        $existingPdfJob = $pdfQueue->getPrintUrlToPdfJob(1);
        $this->assertNull($existingPdfJob);
    }
}
