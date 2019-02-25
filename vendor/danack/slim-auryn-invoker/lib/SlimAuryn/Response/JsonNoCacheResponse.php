<?php

namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class JsonNoCacheResponse implements StubResponse
{
    private $statusCode;

    private $body;

    private $headers = [];

    public function getStatus() : int
    {
        return $this->statusCode;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * DataNoCacheResponse constructor.
     * @param mixed $data - the data, anything that can be json_encoded
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
        // TODO - test for failure to json_encode
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

    public function getBody() : string
    {
        return $this->body;
    }
}
