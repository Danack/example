<?php

namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class TextResponse implements StubResponse
{
    /** @var string */
    private $body;

    private $headers = [];

    /** @var int */
    private $status;

    public function getStatus() : int
    {
        return $this->status;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     *
     * @param string $string
     * @param array $headers
     * @param int $status
     */
    public function __construct(string $string, array $headers = [], int $status = 200)
    {
        $standardHeaders = [
            'Content-Type' => 'text/plain'
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $string;
        $this->status = $status;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}
