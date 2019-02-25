<?php

declare(strict_types=1);

namespace SlimAuryn;

use Slim\App;
use Auryn\Injector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AurynApp extends App
{
    /** @var Injector */
    private $injector;


    /** @var array A list of callables that can map known return types
     * into PSR-7 Response type.
     */
    private $resultMappers;

    /**
     * Add GET route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Slim\Interfaces\RouteInterface
     */
    public function get($pattern, $callable, $setupCallable = null)
    {
        return $this->mapRoute(['GET'], $pattern, $callable, $setupCallable);
    }

    private function mapRoute(array $methods, $pattern, $callable, $setupCallable)
    {
        $actualCallable = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $routeArguments
        ) use (
            $setupCallable,
            $callable
        ) {
            Util::setInjectorInfo($this->injector, $request, $response, $routeArguments);

            if ($setupCallable !== null) {
                // Execute the setup callable
                $this->injector->execute($setupCallable);
            }

            $result = $this->injector->execute($callable);

            return Util::mapResult(
                $result,
                $response,
                $this->resultMappers
            );
        };

        $route = parent::map($methods, $pattern, $actualCallable);

        return $route;
    }

    /**
     * Add POST route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Slim\Interfaces\RouteInterface
     */
    public function post($pattern, $callable)
    {
        return $this->map(['POST'], $pattern, $callable);
    }
}
