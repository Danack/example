<?php

declare(strict_types=1);

namespace Example\CliController;

use Example\Queue\PrintUrlToPdfQueue;

class PrintUrlToPdfQueueProcessor
{
    /** @var PrintUrlToPdfQueue  */
    private $printUrlToPdfQueue;

    /** @var \Example\CliController\PdfGenerator  */
    private $pdfGenerator;


    public function __construct(
        PrintUrlToPdfQueue $invoicePdfQueue,
        PdfGenerator $pdfGenerator
    ) {
        $this->printUrlToPdfQueue = $invoicePdfQueue;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * This is a placeholder background task
     */
    public function run()
    {
        $callable = function () {
            $this->runInternal();
        };

        continuallyExecuteCallable(
            $callable,
            $secondsBetweenRuns = 5,
            $sleepTime = 1,
            $maxRunTime = 600
        );
    }

    public function runInternal()
    {
        $printUrlToPdfJob = $this->printUrlToPdfQueue->getPrintUrlToPdfJob(5);

        if ($printUrlToPdfJob === null) {
            return;
        }

        $this->pdfGenerator->renderUrlAsPdf(
            $printUrlToPdfJob->getUrl(),
            $printUrlToPdfJob->getFilename()
        );
    }
}
