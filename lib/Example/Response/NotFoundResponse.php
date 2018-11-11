<?php


namespace Example\Response;

use Example\Response\Response;

class NotFoundResponse implements Response
{
    private $message;

    const STANDARD_HEADERS = [
        'Content-Type' => 'text/plain'
    ];


    public function __construct($message)
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
        return self::STANDARD_HEADERS;
    }
}
