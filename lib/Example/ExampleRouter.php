<?php

declare(strict_types=1);

namespace Example;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as StdParser;
use Example\Route\Routes;
use Auryn\Injector;
use Slim\Route;
use Slim\Container;

class ExampleRouter implements RouterInterface
{
    /**
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     * Parser
     *
     * @var \FastRoute\RouteParser
     */
    protected $routeParser;

    /** @var Routes */
    private $routes;

    /**
     * Path to fast route cache file. Set to false to disable route caching
     *
     * @var string|False
     */
    protected $cacheFile = false;


    /** @var \Auryn\Injector */
    private $injector;

    /** @var \Slim\Route */
    private $matchedRoute;

    /** @var Container */
    private $container;

    public function __construct(Routes $routes, Injector $injector, Container $container)
    {
        $this->routes = $routes;
        $this->injector = $injector;
        $this->container = $container;
    }

    public function map($methods, $pattern, $handler)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher()
    {
        $this->routeParser = new StdParser();

        if ($this->dispatcher) {
            return $this->dispatcher;
        }

        $routeDefinitionCallback = function (RouteCollector $r) {
            $this->routes->addRoutesToCollector($r);
        };

        if ($this->cacheFile) {
            $this->dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, [
                'routeParser' => $this->routeParser,
                'cacheFile' => $this->cacheFile,
            ]);
        }
        else {
            $this->dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback, [
                'routeParser' => $this->routeParser,
            ]);
        }

        return $this->dispatcher;
    }

    public function dispatch(ServerRequestInterface $request)
    {
        $uri = '/' . ltrim($request->getUri()->getPath(), '/');

        $routeInfo = $this->createDispatcher()->dispatch(
            $request->getMethod(),
            $uri
        );

        // If not found, just return it and let slim handle it.
        if ($routeInfo[0] !== Dispatcher::FOUND) {
            return $routeInfo;
        }

        $callableInfo = $routeInfo[1];
        // Single callable - just return it.
        if (is_array($callableInfo) === false) {
            $this->matchedRoute = new Route([$request->getMethod()], 'whatever', $callableInfo, [], "0");
            $this->matchedRoute->setContainer($this->container); // this makes me sad
            return $routeInfo;
        }

        // We have some callables to prepare.
        for ($i=1; $i<count($callableInfo); $i++) {
            $this->injector->execute($callableInfo[$i]);
        }

        $slimRouteInfo = [
            $routeInfo[0],
            $callableInfo[0],
            $routeInfo[2],
        ];

        $this->matchedRoute = new Route([$request->getMethod()], 'whatever', $callableInfo[0], [], "0");
        $this->matchedRoute->setContainer($this->container); // this makes me sad

        return $slimRouteInfo;
    }


    public function pushGroup($pattern, $callable)
    {
        throw new \Exception('Not implemented pushGroup');
    }

    public function popGroup()
    {
        throw new \Exception('Not implemented popGroup');
    }

    public function getNamedRoute($name)
    {
        throw new \Exception('Not implemented getNamedRoute');
    }

    public function lookupRoute($identifier)
    {
        return $this->matchedRoute;
    }

    public function relativePathFor($name, array $data = [], array $queryParams = [])
    {
        throw new \Exception('Not implemented relativePathFor');
    }

    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        throw new \Exception('Not implemented pathFor');
    }
}
