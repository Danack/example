<?php

declare(strict_types=1);

namespace SlimAuryn;

use Auryn\Injector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Util
 * This class only exists because PHP doesn't have function autoloading.
 */
class Util
{
    public static function mapResult(
        $result,
        ResponseInterface $response,
        array $resultMappers
    ) {
        // Test each of the result mapper, and use an appropriate one.
        foreach ($resultMappers as $type => $mapCallable) {
            if ((is_object($result) && $result instanceof $type) ||
                gettype($result) === $type) {
                return $mapCallable($result, $response);
            }
        }

        // Allow PSR responses to just be passed back.
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // Unknown result type, throw an exception
        $type = gettype($result);
        if ($type === "object") {
            $type = "object of type ". get_class($result);
        }
        $message = sprintf(
            'Dispatched function returned [%s] which is not a type known to the resultMappers.',
            $type
        );
        throw new SlimAurynException($message);
    }

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


        $psr7WithRouteParams = \VarMap\Psr7InputMapWithVarMap::createFromRequestAndVarMap(
            $request,
            new \VarMap\ArrayVarMap($routeArguments)
        );
        $injector->share($psr7WithRouteParams);


        $injector->share($routeParams);
    }
}
