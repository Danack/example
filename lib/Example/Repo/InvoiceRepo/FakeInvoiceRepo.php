<?php

declare(strict_types=1);

namespace Example\Repo\InvoiceRepo;

use Example\Model\Invoice;

class FakeInvoiceRepo implements InvoiceRepo
{
    public function getInvoice($invoiceId) : Invoice
    {
        return new Invoice(0);
    }
}
