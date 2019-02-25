<?php

namespace SlimAuryn\ResponseMapper;

use Psr\Http\Message\ResponseInterface;
use SlimAuryn\Response\StubResponse;

/**
 *
 * When PHP has function autoloading, this will be replaced with a set of functions.
 */
class ResponseMapper
{
    /**
     * Extract the status, headers and body from a StubResponse and
     * set the values on the PSR7 response
     * @param StubResponse $builtResponse
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public static function mapStubResponseToPsr7(
        StubResponse $builtResponse,
        ResponseInterface $response
    ) {
        $response = $response->withStatus($builtResponse->getStatus());
        foreach ($builtResponse->getHeaders() as $key => $value) {
            /** @var \Psr\Http\Message\ResponseInterface $response */
            $response = $response->withHeader($key, $value);
        }
        $response->getBody()->write($builtResponse->getBody());

        return $response;
    }

    /**
     * Just directly return the PSR7 Response without processing
     * @param ResponseInterface $controllerResult
     * @param ResponseInterface $originalResponse
     * @return ResponseInterface
     */
    public static function passThroughResponse(
        ResponseInterface $controllerResult,
        ResponseInterface $originalResponse
    ) {
        return $controllerResult;
    }
}
