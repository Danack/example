<?php

declare(strict_types=1);

namespace Example\Queue;

interface PrintUrlToPdfQueue
{
    public function pushPrintUrlToPdfJob(PrintUrlToPdfJob $pdfJob);

    /**
     * @param $timeout int time to wait for a pdf to render in seconds
     * @return PrintUrlToPdfJob|null
     */
    public function getPrintUrlToPdfJob($timeout): ?PrintUrlToPdfJob;
}
