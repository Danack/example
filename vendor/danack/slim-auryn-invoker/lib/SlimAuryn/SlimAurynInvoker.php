<?php

namespace SlimAuryn;

use Auryn\Injector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SlimAurynInvoker
{
    const SETUP_ARGUMENT_NAME = 'setupCallable';

    /** @var Injector The injector to use for execution */
    private $injector;

    /** @var array A list of callables that can map known return types
     * into PSR-7 Response type.
     */
    private $resultMappers;

    /** @var RouteMiddlewares  */
    private $routeMiddlewares;

    public function __construct(
        Injector $injector,
        RouteMiddlewares $routeMiddlewares,
        array $resultMappers
    ) {
        $this->injector = $injector;
        $this->routeMiddlewares = $routeMiddlewares;
        $this->resultMappers = $resultMappers;
    }

    public function __invoke(
        $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        Util::setInjectorInfo($this->injector, $request, $response, $routeArguments);

        // If the route has a setup callable, call that first.
        $setupCallable = null;
        if (($attribute = $request->getAttribute('route')) !== null) {
            $setupCallable = $attribute->getArgument(self::SETUP_ARGUMENT_NAME, null);

            if ($setupCallable !== null) {
                $this->injector->execute($setupCallable);
            }
        }

        // Wrap the route callable so that it can be called by middlewares
        $fn = function (ServerRequestInterface $request, ResponseInterface $response) use ($callable) {
            $result = $this->injector->execute($callable);

            return Util::mapResult(
                $result,
                $response,
                $this->resultMappers
            );
        };

        // Dispatch the middlewares.
        $result = $this->routeMiddlewares->execute($fn, $request, $response);

        return $result;
    }
}
