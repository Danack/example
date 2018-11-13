<?php

declare(strict_types=1);

namespace Example\AppController;

use Example\Response\HtmlResponse;
use Twig_Environment as Twig;

class Books
{
    public function index(Twig $twig)
    {
        $html = $twig->render('books.html');

        return new HtmlResponse($html);
    }
}
