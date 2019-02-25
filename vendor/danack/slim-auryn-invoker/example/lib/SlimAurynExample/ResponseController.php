<?php

namespace SlimAurynExample;

use SlimAuryn\Response\HtmlResponse;
use Twig_Environment as Twig;

class ResponseController
{
    public function getHomePage(Twig $twig)
    {
        $html = $twig->render('homepage.html');

        return new HtmlResponse($html);
    }
}
