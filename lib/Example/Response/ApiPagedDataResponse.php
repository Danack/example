<?php

namespace Example\Response;

use Example\Api\EntityArray;
use Example\Response\Response;

use Example\Api\Pagination;

class ApiPagedDataResponse implements Response
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

    public function __construct(EntityArray $entityArray, Pagination $pagination, array $headers = [])
    {
        $standardHeaders = [
            'Content-Type' => 'application/json'
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $data = [];
        $data["pagination"] = $pagination->toArray();
        $data[$entityArray->getType()] = $entityArray->toArray();

        $this->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getBody()
    {
        return $this->body;
    }
}
