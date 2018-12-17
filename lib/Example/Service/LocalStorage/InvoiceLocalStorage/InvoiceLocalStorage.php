<?php

declare(strict_types=1);


namespace Example\Service\LocalStorage\InvoiceLocalStorage;

use Example\Model\Invoice;

interface InvoiceLocalStorage
{
    public function isFileAvailable(Invoice $invoice);

    public function getFilename(Invoice $invoice): string;
}
