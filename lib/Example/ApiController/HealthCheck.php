<?php

declare(strict_types=1);

namespace Example\ApiController;

use Example\Response\DataResponse;

class HealthCheck
{
    public function get()
    {
        return new DataResponse(['ok']);
    }
}
