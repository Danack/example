<?php

declare(strict_types=1);

namespace SlimAurynExample;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * A null middleware for testing.
 * Person_1) This doesn't do anything.
 * Person_2) No, it does NOTHING!
 */
class NullMiddleware
{
    private $wasCalled = false;

    public function __invoke(Request $request, ResponseInterface $response, $next)
    {
        $this->wasCalled = true;

        return $next($request, $response);
    }

    /**
     * @return bool
     */
    public function wasCalled(): bool
    {
        return $this->wasCalled;
    }
}
