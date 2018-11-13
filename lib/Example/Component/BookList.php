<?php

declare(strict_types=1);

namespace Example\Component;

class BookList
{
    /** @var \Example\Repo\BookListRepo\BookListRepo */
    private $bookListRepo;

    /**
     * BookList constructor.
     * @param \Example\Repo\BookListRepo\BookListRepo $bookListRepo
     */
    public function __construct(\Example\Repo\BookListRepo\BookListRepo $bookListRepo)
    {
        $this->bookListRepo = $bookListRepo;
    }

    public function render()
    {
        $books = $this->bookListRepo->getAllBooks();

        $tableBodyHtml = '';
        $rowHtml = <<< HTML
<tr>
    <td><a href=":attr_link">:html_name</a></td>
    <td>:html_author</td>
</tr>
HTML;
        foreach ($books as $book) {
            $params = [
                ':html_name' => $book->getName(),
                ':attr_link' => $book->getLink(),
                ':html_author' => $book->getAuthor(),
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
