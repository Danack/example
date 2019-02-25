<?php

declare(strict_types=1);

namespace SlimAurynExample;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * A middleware that is setup for all routes, for testing.
 */
class AllRoutesMiddleware
{
    public function __invoke(Request $request, ResponseInterface $response, $next)
    {
        $response = $next($request, $response);

        /** @var ResponseInterface $response */
        $response = $response->withAddedHeader('X-all_routes_middleware', 'active');

        return $response;
    }
}
