<?php


namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class NotFoundResponse implements StubResponse
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getStatus() : int
    {
        return 404;
    }

    public function getBody() : string
    {
        return $this->message;
    }

    public function getHeaders() : array
    {
        return [];
    }
}
