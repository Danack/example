<?php

namespace Danack\Response;

use Danack\Response\StubResponse;

class HtmlResponse implements StubResponse
{
    private $body;

    private $headers = [];

    public function getStatus()
    {
        return 200;
    }

    public function getHeaders()
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

    public function getBody()
    {
        return $this->body;
    }
}
