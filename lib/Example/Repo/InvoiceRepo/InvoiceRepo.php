<?php

declare(strict_types=1);

namespace Example\Repo\InvoiceRepo;

use Example\Model\Invoice;

interface InvoiceRepo
{
    public function getInvoice($invoiceId) : Invoice;
}
