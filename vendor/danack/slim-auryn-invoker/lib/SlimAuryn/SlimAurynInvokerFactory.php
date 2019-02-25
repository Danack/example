<?php

namespace SlimAuryn;

use Auryn\Injector;
use Pimple\Container;
use SlimAuryn\SlimAurynInvoker;

class SlimAurynInvokerFactory
{
    /** @var Injector  */
    private $injector;

    /** @var RouteMiddlewares  */
    private $routeMiddlewares;

    /** @var array A set of callables that can convert from the type
     * returned by a controller, to a PSR 7 response type
     */
    private $resultMappers;

    /**
     * SlimAurynInvokerFactory constructor.
     * @param Injector $injector
     * @param array $resultMappers
     */
    public function __construct(
        Injector $injector,
        RouteMiddlewares $routeMiddlewares,
        array $resultMappers
    ) {
        $this->injector = $injector;
        $this->routeMiddlewares = $routeMiddlewares;
        $this->resultMappers = $resultMappers;
    }

    /**
     * @param Container $container
     * @return \SlimAuryn\SlimAurynInvoker
     */
    public function __invoke(Container $container)
    {
        return new SlimAurynInvoker(
            $this->injector,
            $this->routeMiddlewares,
            $this->resultMappers
        );
    }
}
