<?php

namespace Example\Response;

use Example\Response\Response;

class DataResponse implements Response
{
    private $body;

    private $headers = [];

    private $statusCode;

    const STANDARD_HEADERS = [
        'Content-Type' => 'application/json'
    ];

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
     * @param mixed $data  The json_encodable data
     * @param array $headers
     */
    public function __construct($data, array $headers = [], int $statusCode = 200)
    {
        $this->headers = array_merge(self::STANDARD_HEADERS, $headers);
        $this->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }
}
