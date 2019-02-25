<?php

declare(strict_types=1);

namespace SlimAuryn;

use SlimAuryn\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExceptionMiddleware
{
    /**
     * @var array
     * Convert particular exceptions to responses
     */
    private $exceptionMappers;

    /**
     * @var array
     * Map custom results/responses to PSR7Responses
     */
    private $resultMappers;

    public function __construct($exceptionHandlers, $resultMappers)
    {
        $this->exceptionMappers = $exceptionHandlers;
        $this->resultMappers = $resultMappers;
    }

    public function __invoke(Request $request, ResponseInterface $response, $next)
    {
        try {
            /** @var ResponseInterface $response */
            $response = $next($request, $response);

            return $response;
        }
        catch (\Throwable $e) {
            // Find if there is an exception handler for this type of exception
            foreach ($this->exceptionMappers as $type => $exceptionCallable) {
                if ($e instanceof $type) {
                    $exceptionResult = $exceptionCallable($e, $response);

                    return Util::mapResult(
                        $exceptionResult,
                        $response,
                        $this->resultMappers
                    );
                }
            }
            // No exception handler for this exception type, so rethrow the
            // exception to allow it to propagate up the stack.
            throw $e;
        }
    }
}
