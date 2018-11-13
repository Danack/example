<?php

declare(strict_types=1);

namespace Example\Repo\BookListRepo;

use Example\Model\Book;

interface BookListRepo
{
    /** @return Book[] */
    public function getAllBooks();
}
