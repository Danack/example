<?php

declare(strict_types=1);

namespace Example\Component;

use Example\Repo\InvoiceRepo\FakeInvoiceRepo;
use Example\Model\Invoice;

class InvoiceList
{
    public function render()
    {
        $repo = new FakeInvoiceRepo();

        /** @var  $invoices Invoice[] */
        $invoices = [
            $repo->getInvoice(0)
        ];

        $tableBodyHtml = '';
        $rowHtml = <<< HTML
<tr>
  <td>Invoice :html_invoice_id</td>
  <td>
    <span class="invoice_download" data-invoice_url=":attr_invoice_url">
     
    </span>
  </td>
</tr>

HTML;
        foreach ($invoices as $invoice) {
            $params = [
                ':html_invoice_id' => $invoice->getId(),
                ':attr_invoice_url' => buildInvoicePrepareLink($invoice)
            ];

            $tableBodyHtml .= esprintf($rowHtml, $params);
        }

        $html = <<< HTML
<table>
  <thead>
   <th>Name</th>
   <th>Author</th>  
  </thead>
  <tbody>
    $tableBodyHtml
  </tbody>
</table>
HTML;

        return $html;
    }
}
