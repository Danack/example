<?php

namespace Example\Response;

use Example\Response\Response;

class RedirectResponse implements Response
{
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
     */
    public function __construct($uri, int $statusCode = 303, array $headers = [])
    {
        $standardHeaders = [
            'location' => $uri
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        return "";
    }
}
