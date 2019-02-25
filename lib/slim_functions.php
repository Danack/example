<?php

declare(strict_types=1);

use Auryn\Injector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SlimAuryn\RouteParams as InvokerRouteParams;

function getReasonPhrase(int $status)
{
    $knownStatusReasons = [
        420 => 'Enhance Your Calm',
        421 => 'what the heck'
    ];

    return $knownStatusReasons[$status] ?? '';
}

function exampleResponseMapper(\Example\Response\Response $builtResponse, ResponseInterface $response)
{
    $status = $builtResponse->getStatus();
    $reasonPhrase = getReasonPhrase($status);

    $response = $response->withStatus($status, $reasonPhrase);
    foreach ($builtResponse->getHeaders() as $key => $value) {
        $response = $response->withAddedHeader($key, $value);
    }
    $response->getBody()->write($builtResponse->getBody());

    return $response;
}
