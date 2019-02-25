<?php

declare(strict_types=1);

namespace Example\AppController;

use SlimAuryn\Response\TwigResponse;

class Pages
{
    public function wordSearch()
    {
        return new TwigResponse('pages/word_search.html');
    }
}
