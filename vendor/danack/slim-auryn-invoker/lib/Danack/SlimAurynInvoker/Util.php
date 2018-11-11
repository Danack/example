<?php

namespace Danack\SlimAurynInvoker;

use Auryn\Injector;
use Danack\Response\StubResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Helper function for the library
 * This class only exists because we don't have function autoloading
 * in PHP.
 */
class Util
{
    /**
     * Put information about the request/response into the injector
     * @param Injector $injector
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $routeArguments
     */
    public static function setInjectorInfo(
        Injector $injector,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        $injector->alias(ServerRequestInterface::class, get_class($request));
        $injector->share($request);
        $injector->alias(ResponseInterface::class, get_class($response));
        $injector->share($response);
        foreach ($routeArguments as $key => $value) {
            $injector->defineParam($key, $value);
        }

        $routeParams = new RouteParams($routeArguments);
        $injector->share($routeParams);
    }
}
