<?php

declare(strict_types=1);

namespace Example\AppController;

use Example\Response\HtmlResponse;
use Twig_Environment as Twig;
use SlimAuryn\Response\TwigResponse;

class Index
{
    public function get(Twig $twig)
    {
        $html = $twig->render('index.html');

        return new HtmlResponse($html);
    }

    public function getTwig()
    {
        return new TwigResponse('index.html');
    }

    public function get404()
    {
        return new TwigResponse('404.html', [], 404);
    }
}
