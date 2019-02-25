<?php

namespace SlimAuryn\Response;

class EmptyResponse implements StubResponse
{
    private $headers;

    private $statusCode;

    const DEFAULT_STATUS = 204;

    public function __construct(array $headers = [], int $statusCode = self::DEFAULT_STATUS)
    {
        $standardHeaders = [];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->statusCode = $statusCode;
    }

    public function getStatus() : int
    {
        return $this->statusCode;
    }

    public function getBody() : string
    {
        return "";
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }
}
