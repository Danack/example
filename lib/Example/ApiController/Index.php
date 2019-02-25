<?php

declare(strict_types=1);

namespace Example\ApiController;

use SlimAuryn\Response\JsonResponse;

class Index
{
    public function get404()
    {
        return new JsonResponse('Route not found.', [], 404);
    }
}
