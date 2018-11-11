<?php

declare(strict_types=1);

namespace Example;

use Slim\Container;

class SlimNotFoundHandler
{
    /** @var Container  */
    private $c;

    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    public function __invoke($request, $response)
    {
        $response = [
            'status' => 'notfound',
        ];

        return $this->c['response']->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write(\json_encode($response, JSON_PRETTY_PRINT));
    }
}
