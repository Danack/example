<?php

declare(strict_types=1);

namespace Example\AppController;

use Example\Response\HtmlResponse;
use Twig_Environment as Twig;

class Index
{
    public function get(Twig $twig)
    {
        $html = $twig->render('index.html');

        return new HtmlResponse($html);
    }
}
