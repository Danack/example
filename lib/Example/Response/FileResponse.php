<?php

declare(strict_types=1);

namespace Example\Response;

use Example\Response\Response;

class FileResponse implements Response
{
    /** @var array  */
    private $headers;

    private $filehandle;

    public function __construct($filenameToServe, $userFacingFilename, array $headers = [])
    {
        $standardHeaders = [
            'Content-Type' => getMimeTypeFromFilename($filenameToServe),
            'Content-Disposition' => 'attachment; filename="' . $userFacingFilename . '"'
        ];

        $this->headers = array_merge($standardHeaders, $headers);

        $this->filehandle = @fopen($filenameToServe, 'r');

        if ($this->filehandle === false) {
            throw new \Exception("Failed to open file for serving.");
        }
    }

    public function getStatus()
    {
        return 200;
    }

    // if we ever care about not reading the whole file into memory first
    // this function could just emit to output, with appropriate changes in
    // the response mapper
    public function getBody()
    {
        return stream_get_contents($this->filehandle);
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
