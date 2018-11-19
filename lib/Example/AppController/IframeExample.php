<?php

declare(strict_types=1);

namespace Example\AppController;

use Example\Response\HtmlResponse;
use Twig_Environment as Twig;

class IframeExample
{
    public function iframeContainer(Twig $twig)
    {
        $html = $twig->render('iframe/container.html');
        return new HtmlResponse($html);
    }


    public function iframeContents(Twig $twig)
    {
        $html = $twig->render('iframe/contents.html');
        return new HtmlResponse($html);
    }
}
