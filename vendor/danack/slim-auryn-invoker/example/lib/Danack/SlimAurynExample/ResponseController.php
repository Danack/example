<?php

namespace Danack\SlimAurynExample;

use Danack\Response\HtmlResponse;
use Twig_Environment as Twig;

class ResponseController
{
    public function getHomePage(Twig $twig)
    {
        $html = $twig->render('hompage.html');

        return new HtmlResponse($html);
    }
}
