<?php


namespace Danack\Response;

use Danack\Response\StubResponse;

class NotFoundResponse implements StubResponse
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getStatus()
    {
        return 404;
    }

    public function getBody()
    {
        return $this->message;
    }

    public function getHeaders()
    {
        return [];
    }
}
