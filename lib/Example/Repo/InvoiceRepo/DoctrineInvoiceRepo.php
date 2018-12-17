<?php

declare(strict_types=1);

namespace Example\Repo\InvoiceRepo;

use Doctrine\ORM\EntityManager;
use Example\Model\Invoice;
use Example\Exception\InvoiceNotFoundException;

class DoctrineInvoiceRepo implements InvoiceRepo
{
    /** @var EntityManager */
    private $em;

    /**
     * DoctrineBookListRepo constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getInvoice($invoiceId) : Invoice
    {
        $repo = $this->em->getRepository(Invoice::class);
        $invoice = $repo->find($invoiceId);

        if ($invoice === null) {
            throw new InvoiceNotFoundException("Invoice [$invoiceId] not found.");
        }
    }
}
