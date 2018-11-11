<?php

namespace Danack\SlimAurynInvoker;

/**
 * Class RouteParams - contains all of the parameters that were present in the matched route
 *
 */
class RouteParams
{
    private $args;

    /**
     * RouteParams constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->args = $args;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasValue(string $key)
    {
        return array_key_exists($key, $this->args);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function getValue(string $key)
    {
        if (array_key_exists($key, $this->args) === false) {
            $message = "Key [$key] does not exist - please use hasValue to check if it is present first.";

            throw new RouteParamsException($message);
        }

        return $this->args[$key];
    }
}
