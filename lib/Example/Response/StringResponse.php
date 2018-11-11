<?php

namespace Example\Response;

use Example\Response\Response;

class StringResponse implements Response
{
    private $body;

    private $headers = [];

    const STANDARD_HEADERS = [
        'Content-Type' => 'text/plain'
    ];

    public function getStatus()
    {
        return 200;
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
    public function __construct($string, array $headers = [])
    {
        $this->headers = array_merge(self::STANDARD_HEADERS, $headers);
        $this->body = $string;
    }

    public function getBody()
    {
        return $this->body;
    }
}
