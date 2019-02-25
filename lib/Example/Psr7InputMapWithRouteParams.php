<?php

namespace Example;

use Psr\Http\Message\ServerRequestInterface;
use SlimAuryn\RouteParams;
use VarMap\VarMap;

class Psr7InputMapWithRouteParams implements VarMap
{
    /** @var ServerRequestInterface */
    private $serverRequest;

    /** @var RouteParams */
    private $routeParams;

    public function __construct(ServerRequestInterface $serverRequest, RouteParams $routeParams)
    {
        $this->serverRequest = $serverRequest;
        $this->routeParams = $routeParams;
    }

    /**
     * @inheritdoc
     */
    public function getWithDefault(string $variableName, $defaultValue)
    {
        $queryParams = $this->serverRequest->getQueryParams();
        if (array_key_exists($variableName, $queryParams) === true) {
            return $queryParams[$variableName];
        }

        $bodyParams = $this->serverRequest->getParsedBody();
        if (is_array($bodyParams) && array_key_exists($variableName, $bodyParams) === true) {
            return $bodyParams[$variableName];
        }

        if ($this->routeParams->hasValue($variableName) === true) {
            return $this->routeParams->getValue($variableName);
        }

        return $defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function has(string $variableName) : bool
    {
        $queryParams = $this->serverRequest->getQueryParams();
        if (array_key_exists($variableName, $queryParams) === true) {
            return true;
        }

        $bodyParams = $this->serverRequest->getParsedBody();
        if (is_array($bodyParams) && array_key_exists($variableName, $bodyParams) === true) {
            return true;
        }

        if ($this->routeParams->hasValue($variableName) === true) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function get(string $variableName)
    {
        $queryParams = $this->serverRequest->getQueryParams();
        if (array_key_exists($variableName, $queryParams) === true) {
            return $queryParams[$variableName];
        }

        $bodyParams = $this->serverRequest->getParsedBody();
        if (is_array($bodyParams) && array_key_exists($variableName, $bodyParams) === true) {
            return $bodyParams[$variableName];
        }

        return $this->routeParams->getValue($variableName);
    }

    /**
     * @inheritdoc
     */
    public function getNames()
    {
        $requestVariableNames = [];

        $requestParams = $this->serverRequest->getParsedBody();
        if (is_array($requestParams) == true) {
            $requestVariableNames = array_keys($requestParams);
        }

        $routeParamsArray = $this->routeParams->getAll();

        return array_merge($requestVariableNames, array_keys($routeParamsArray));
    }
}
