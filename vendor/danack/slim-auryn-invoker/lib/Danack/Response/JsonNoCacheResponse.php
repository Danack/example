<?php

namespace Danack\Response;

use Danack\Response\StubResponse;

class JsonNoCacheResponse implements StubResponse
{
    private $statusCode;

    private $body;

    private $headers = [];

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * DataNoCacheResponse constructor.
     * @param $data
     * @param array $headers
     * @throws InvalidDataException
     */
    public function __construct($data, array $headers = [], int $statusCode = 200)
    {
        $standardHeaders = [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, no-store',
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->statusCode = $statusCode;

        if ($this->body === false) {
            $message = sprintf(
                "Failed to convert array to JSON with error %s:%s",
                json_last_error(),
                json_last_error_msg()
            );

            throw new InvalidDataException($message);
        }
    }

    public function getBody()
    {
        return $this->body;
    }
}
