<?php

namespace Danack\SlimAurynInvoker;

use Auryn\Injector;
use Pimple\Container;
use Danack\SlimAurynInvoker\SlimAurynInvoker;

class SlimAurynInvokerFactory
{
    /** @var Injector  */
    private $injector;

    /** @var array A set of callables that can convert from the type
     * returned by a controller, to a PSR 7 response type
     */
    private $resultMappers;

    /**
     * @var callable
     */
    private $setupFunction;

    /**
     * An array of string => callable exception handlers.
     *
     * @var array
     */
    private $exceptionHandlers;

    /**
     * SlimAurynInvokerFactory constructor.
     * @param Injector $injector
     * @param array|null $resultMappers
     */
    public function __construct(
        Injector $injector,
        array $resultMappers = null,
        callable $setupFunction = null,
        array $exceptionHandlers = null
    ) {
        $this->injector = $injector;
        $this->resultMappers = $resultMappers;
        $this->setupFunction = $setupFunction;
        $this->exceptionHandlers = $exceptionHandlers;
    }

    /**
     * @param Container $container
     * @return \Danack\SlimAurynInvoker\SlimAurynInvoker
     */
    public function __invoke(Container $container)
    {
        return new SlimAurynInvoker(
            $this->injector,
            $this->resultMappers,
            $this->setupFunction,
            $this->exceptionHandlers
        );
    }
}
