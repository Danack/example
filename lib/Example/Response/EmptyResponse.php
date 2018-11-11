<?php

namespace Example\Response;

use Example\Response\Response;

class EmptyResponse implements Response
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

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return "";
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
