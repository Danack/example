<?php

namespace Danack\SlimAurynInvoker;

use Auryn\Injector;
use Danack\Response\StubResponse;
use Danack\Response\StubResponseMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SlimAurynInvoker
{
    /** @var Injector The injector to use for execution */
    private $injector;

    /** @var array A list of callables that can map known return types
     * into PSR-7 Response type.
     */
    private $resultMappers;

    /** @var  array */
    private $exceptionHandlers;

    /**
     * SlimAurynInvoker constructor.
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
        if ($resultMappers !== null) {
            $this->resultMappers = $resultMappers;
        }
        // Default to using: i) a StubResponse mapper, ii) if a PSR response is
        // returned, just pass that back.
        else {
            $this->resultMappers = [
                StubResponse::class => [StubResponseMapper::class, 'mapToPsr7Response'],
                ResponseInterface::class => function (
                    ResponseInterface $controllerResult,
                    ResponseInterface $originalResponse
                ) {
                    return $controllerResult;
                }
            ];
        }

        if ($setupFunction === null) {
            $this->setupFunction = [Util::class, 'setInjectorInfo'];
        }
        else {
            $this->setupFunction = $setupFunction;
        }
        $this->exceptionHandlers = [];
        if ($exceptionHandlers !== null) {
            $this->exceptionHandlers = $exceptionHandlers;
        }
    }


    private function mapResult($result, ResponseInterface $response)
    {
        // Test each of the result mapper, and use an appropriate one.
        foreach ($this->resultMappers as $type => $mapCallable) {
            if ((is_object($result) && $result instanceof $type) ||
                gettype($result) === $type) {
                return $mapCallable($result, $response);
            }
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
        throw new SlimAurynInvokerException($message);
    }

    /**
     * @param $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $routeArguments
     * @return mixed
     * @throws SlimAurynInvokerException
     */
    public function __invoke(
        $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        $fn = $this->setupFunction;
        $fn($this->injector, $request, $response, $routeArguments);

        try {
            // Execute the callable
            $result = $this->injector->execute($callable);

            return $this->mapResult($result, $response);
        }
        catch (\Exception $e) {
            // Find if there is an exception handler for this type of exception
            foreach ($this->exceptionHandlers as $type => $exceptionCallable) {
                if ($e instanceof $type) {
                    $exceptionResult = $exceptionCallable($e, $response);
                    return $this->mapResult($exceptionResult, $response);
                }
            }
            // No exception handler for this exception type, so rethrow the
            // exception to allow it to propagate up the stack.
            throw $e;
        }
    }
}
