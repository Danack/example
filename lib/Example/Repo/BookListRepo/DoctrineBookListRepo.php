<?php

declare(strict_types=1);

namespace Example\Repo\BookListRepo;

use Doctrine\ORM\EntityManager;
use Example\Model\Book;

class DoctrineBookListRepo implements BookListRepo
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

    /**
     * @return Book[]
     */
    public function getAllBooks()
    {
        $repo = $this->em->getRepository(Book::class);

        return $repo->findAll();
    }
}
