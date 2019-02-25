<?php

declare(strict_types=1);

namespace SlimAuryn;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

class RouteMiddlewares
{
    private $middlewareList = [];

    public function addMiddleware($middleware)
    {
        $this->middlewareList[] = $middleware;
    }

    public function execute(
        $callable,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $currentCallable = $callable;

        foreach ($this->middlewareList as $middleware) {
            $middlewareCallable = function (
                ServerRequestInterface $req,
                ResponseInterface $res
            ) use (
                $middleware,
                $currentCallable
            ) {
                $result = call_user_func($middleware, $req, $res, $currentCallable);
                if ($result instanceof ResponseInterface === false) {
                    throw new UnexpectedValueException(
                        'Middleware must return instance of \Psr\Http\Message\ResponseInterface'
                    );
                }

                return $result;
            };

            $currentCallable = $middlewareCallable;
        }

        $result = call_user_func(
            $currentCallable,
            $request,
            $response
        );

        if ($result instanceof ResponseInterface === false) {
            throw new UnexpectedValueException(
                'Middleware must return instance of \Psr\Http\Message\ResponseInterface'
            );
        }

        return $result;
    }
}
