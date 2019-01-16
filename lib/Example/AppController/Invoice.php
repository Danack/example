<?php

declare (strict_types = 1);

namespace Example\AppController;

use Example\Queue\PrintUrlToPdfJob;
use Example\Response\DataNoCacheResponse;
use Example\Response\DataResponse;
use Twig_Environment as Twig;
use Example\Response\FileResponse;
use Example\Queue\PrintUrlToPdfQueue;
use Example\Repo\InvoiceRepo\InvoiceRepo;
use Example\Response\HtmlResponse;
use Example\Response\NotFoundResponse;
use Example\Service\LocalStorage\InvoiceLocalStorage\InvoiceLocalStorage;

class Invoice
{
    /**
     * @param InvoiceRepo $invoiceRepo
     * @param $invoice_id
     */
    public function renderInvoice(
        InvoiceRepo $invoiceRepo,
        $invoice_id
    ) {
        $html = <<< HTML
  <html>
    <body>
      Hello, I am invoice.<br/>
      Pay me.<br/>
</body>
</html>

HTML;

        return new HtmlResponse($html);
    }



    public function listInvoices(Twig $twig)
    {
        $html = $twig->render('invoices.html');

        return new HtmlResponse($html);
    }


    /**
     * @param PrintUrlToPdfQueue $printUrlToPdfQueue
     * @param InvoiceRepo $invoiceRepo
     * @param InvoiceLocalStorage $localInvoiceStorage
     * @param $invoice_id
     * @return DataNoCacheResponse
     */
    public function generateOrGetDownloadLink(
        PrintUrlToPdfQueue $printUrlToPdfQueue,
        InvoiceRepo $invoiceRepo,
        InvoiceLocalStorage $localInvoiceStorage,
        $invoice_id
    ) {
        $invoice = $invoiceRepo->getInvoice($invoice_id);
        $isAvailable = $localInvoiceStorage->isFileAvailable($invoice);

        if ($isAvailable === true) {
            return new DataNoCacheResponse([
                'status' => 'generated',
                'url' => buildInvoiceDownloadLink($invoice)
            ]);
        }

        // TODO - validate user allowed access to $newsletter_id
        $pdfJob = new PrintUrlToPdfJob(
            buildInvoiceRenderLink($invoice),
            $localInvoiceStorage->getFilename($invoice)
        );
        $printUrlToPdfQueue->pushPrintUrlToPdfJob($pdfJob);

        return new DataNoCacheResponse(['status' => 'generating']);
    }

    /**
     * @param InvoiceRepo $invoiceRepo
     * @param InvoiceLocalStorage $localInvoiceStorage
     * @param $invoice_id
     * @return FileResponse|NotFoundResponse
     */
    public function downloadInvoice(
        InvoiceRepo $invoiceRepo,
        InvoiceLocalStorage $localInvoiceStorage,
        $invoice_id
    ) {
        $invoice = $invoiceRepo->getInvoice($invoice_id);
        $isAvailable = $localInvoiceStorage->isFileAvailable($invoice);

        if ($isAvailable !== true) {
            return new NotFoundResponse("Invoice not generated yet.");
        }

        return new FileResponse(
            $localInvoiceStorage->getFilename($invoice),
            'invoice_' . $invoice->getId() . '.pdf'
        );
    }
}
