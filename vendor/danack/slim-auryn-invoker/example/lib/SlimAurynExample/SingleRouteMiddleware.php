<?php

declare(strict_types=1);

namespace SlimAurynExample;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class SingleRouteMiddleware
{
    public function __invoke(Request $request, ResponseInterface $response, $next)
    {
        $response = $next($request, $response);

        /** @var ResponseInterface $response */
        $response = $response->withAddedHeader('X-single_route_middleware', 'active');

        return $response;
    }
}
