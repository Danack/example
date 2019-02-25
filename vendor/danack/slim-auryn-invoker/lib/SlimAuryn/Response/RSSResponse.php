<?php

namespace Example\Response;

namespace SlimAuryn\Response;

class RSSResponse implements StubResponse
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
     * XMLResponse constructor.
     * @param string $xml
     * @param array $headers
     */
    public function __construct(string $xml, array $headers = [])
    {
        $standardHeaders = [
            'Content-Type' => 'application/rss+xml; charset=utf-8'
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $xml;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}
