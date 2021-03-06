<?php

namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class RedirectResponse implements StubResponse
{
    private $headers = [];

    private $statusCode;

    /**
     * RedirectResponse constructor.
     * @param string $uri
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $uri, int $statusCode = 302, array $headers = [])
    {
        $standardHeaders = [
            'Location' => $uri
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->statusCode = $statusCode;
    }

    public function getStatus() : int
    {
        return $this->statusCode;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getBody() : string
    {
        return "";
    }
}
