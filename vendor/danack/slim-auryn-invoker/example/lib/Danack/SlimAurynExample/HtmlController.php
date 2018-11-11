<?php

namespace Danack\SlimAurynExample;

use Twig_Environment as Twig;

class HtmlController
{
    public function getPage(Twig $twig) : string
    {
        return $twig->render('string_example.html');
    }
}
