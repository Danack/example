<?php

declare(strict_types=1);

namespace Example\ApiController;

use Slim\Http\Response;

class Options
{
    public function proxyOptions(Response $response)
    {
        $response = $response->withHeader("Access-Control-Allow-Origin", "*");
        $response = $response->withHeader("Allow", "HEAD,GET,PUT,DELETE,OPTIONS");
        $response = $response->withHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
