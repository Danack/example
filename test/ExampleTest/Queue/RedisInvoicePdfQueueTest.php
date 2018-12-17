<?php

declare(strict_types=1);

namespace ExampleTest\Queue;

use Example\Model\Invoice;
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

        $invoiceId = 312321312312312312313;

        $invoice = new Invoice($invoiceId);
        $invoicePdfJob = new PrintUrlToPdfJob($invoice);
        $pdfQueue->pushPrintUrlToPdfJob($invoicePdfJob);

        $invoiceFromQueue = $existingPdfJob = $pdfQueue->getPrintUrlToPdfJob(5);
        $this->assertEquals($invoiceId, $invoiceFromQueue);
    }
}
