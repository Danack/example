<?php

namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class HtmlResponse implements StubResponse
{
    private $body;

    private $headers = [];

    public function getStatus() : int
    {
        return 200;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * HtmlResponse constructor.
     * @param string $html
     * @param array $headers
     */
    public function __construct(string $html, array $headers = [])
    {
        $standardHeaders = [
            'Content-Type' => 'text/html'
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $html;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}
