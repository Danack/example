<?php

declare(strict_types=1);

namespace Example\Response;

use Example\Response\Response;

class ServerErrorResponse implements Response
{
    /** @var string  */
    private $body;

    /** @var array */
    private $headers = [];

    /** @var int  */
    private $statusCode;

    const STANDARD_HEADERS = [
       'Content-Type' => 'text/plain'
    ];

    /**
     * JsonResponse constructor.
     * @param $data
     * @param array $headers
     */
    public function __construct(string $text, int $statusCode, array $headers = [])
    {
        $this->headers = array_merge(self::STANDARD_HEADERS, $headers);
        $this->body = $text;
        $this->statusCode = $statusCode;
    }

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
