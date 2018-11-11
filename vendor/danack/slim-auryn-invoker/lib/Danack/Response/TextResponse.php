<?php

namespace Danack\Response;

use Danack\Response\StubResponse;

class TextResponse implements StubResponse
{
    private $body;

    private $headers = [];

    private $status;

    public function getStatus()
    {
        return $this->status;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * JsonResponse constructor.
     * @param $data
     * @param array $headers
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

    public function getBody()
    {
        return $this->body;
    }
}
