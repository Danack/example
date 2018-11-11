<?php

namespace VarMap;

use Psr\Http\Message\ServerRequestInterface;
use VarMap\VarMap;

class Psr7InputMapWithVarMap implements VarMap
{
    /** @var ServerRequestInterface */
    private $serverRequest;

    /** @var VarMap */
    private $varMap;

    /**
     * Psr7InputMapWithVarMap constructor.
     *
     * Private to prevent 'yo dawgging' errors.
     *
     * @param ServerRequestInterface $serverRequest
     * @param \VarMap\VarMap $routeParams
     */
    private function __construct(ServerRequestInterface $serverRequest, VarMap $routeParams)
    {
        $this->serverRequest = $serverRequest;
        $this->varMap = $routeParams;
    }

    /**
     * Use this static factory method to explicitly create this object from an existing
     * VarMap
     */
    public static function createFromRequestAndVarMap(ServerRequestInterface $serverRequest, VarMap $varMap)
    {
        return new self($serverRequest, $varMap);
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

        if ($this->varMap->has($variableName) === true) {
            return $this->varMap->get($variableName);
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

        if ($this->varMap->has($variableName) === true) {
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

        return $this->varMap->get($variableName);
    }

    /**
     * @inheritdoc
     */
    public function getNames()
    {
        $requestVariableNames = array_keys($this->serverRequest->getParsedBody());
        $varmapNames = $this->getNames();

        return array_merge($requestVariableNames, $varmapNames);
    }
}
