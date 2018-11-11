<?php

declare(strict_types=1);

namespace Example\AdminController;

use Example\Response\HtmlResponse;

class Index
{
    public function get()
    {
        return new HtmlResponse("admin site goes here");
    }
}
