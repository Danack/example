<?php

namespace VarMap;

use VarMap\Exception\VariableMissingException;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\VarMap;

class Psr7VarMap implements VarMap
{
    /** @var ServerRequestInterface */
    private $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
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

        throw VariableMissingException::create($variableName);
    }

    /**
     * @inheritdoc
     */
    public function getNames()
    {
        $params = $this->serverRequest->getQueryParams();

        $params = array_keys($params);

        $body = $this->serverRequest->getParsedBody();

        if (is_array($body) === true) {
            $params = array_merge($params, array_keys($body));
        }

        return $params;
    }
}
