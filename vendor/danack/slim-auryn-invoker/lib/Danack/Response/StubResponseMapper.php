<?php

namespace Danack\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class StubResponseMapper
 *
 * When PHP has function autoloading, this will be replaced with a function.
 */
class StubResponseMapper
{
    /**
     * @param StubResponse $builtResponse
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public static function mapToPsr7Response(StubResponse $builtResponse, ResponseInterface $response)
    {
        $response = $response->withStatus($builtResponse->getStatus());
        foreach ($builtResponse->getHeaders() as $key => $value) {
            /** @var $response \Psr\Http\Message\ResponseInterface */
            $response = $response->withHeader($key, $value);
        }
        $response->getBody()->write($builtResponse->getBody());

        return $response;
    }
}
