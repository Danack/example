<?php

namespace Danack\Response;

use Danack\Response\StubResponse;
use Danack\Response\InvalidDataException;

class JsonResponse implements StubResponse
{
    private $body;

    private $headers = [];

    private $statusCode;

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * JsonResponse constructor.
     * @param $data
     * @param array $headers
     * @param int $statusCode
     * @throws \Danack\Response\InvalidDataException
     */
    public function __construct($data, array $headers = [], int $statusCode = 200)
    {
        $standardHeaders = [
            'Content-Type' => 'application/json'
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->statusCode = $statusCode;
        $this->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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
