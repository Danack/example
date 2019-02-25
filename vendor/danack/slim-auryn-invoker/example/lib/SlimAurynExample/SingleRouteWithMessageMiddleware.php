<?php

declare(strict_types=1);

namespace SlimAurynExample;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class SingleRouteWithMessageMiddleware
{
    const HEADER_NAME = 'X-ordered_middleware';


    /** @var string */
    private $message;

    /**
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    private function createOrReplaceHeaderLine(ResponseInterface $response)
    {
        if ($response->hasHeader(self::HEADER_NAME) === true) {
            $existingHeader = $response->getHeaderLine(self::HEADER_NAME);
            $newHeader = $existingHeader . ', ' . $this->message;
            return $response->withHeader(self::HEADER_NAME, $newHeader);
        }

        return $response->withAddedHeader(self::HEADER_NAME, $this->message);
    }

    public function __invoke(Request $request, ResponseInterface $response, $next)
    {
        $response = $next($request, $response);

        /** @var ResponseInterface $response */
        $response = $this->createOrReplaceHeaderLine($response);

        return $response;
    }
}
