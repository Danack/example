<?php

namespace Example\Response;

use Example\Response\Response;

class JsonAttachmentResponse implements Response
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
     */
    public function __construct($data, $filename = 'export.json', array $headers = [], int $statusCode = 200)
    {
        $this->data = $data;

        $standardHeaders = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }
}
