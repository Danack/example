<?php

namespace SlimAuryn\Response;

class HtmlNoCacheResponse implements StubResponse
{
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
     * HtmlResponse constructor.
     * @param string $html
     * @param array $headers
     */
    public function __construct(string $html, array $headers = [], int $status = 200)
    {
        $standardHeaders = [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store',
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $html;
        $this->status = $status;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}
