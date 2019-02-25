<?php

declare(strict_types=1);

namespace SlimAuryn\Response;

use SlimAuryn\SlimAurynException;

class FileResponse implements StubResponse
{
    /** @var array  */
    private $headers;

    private $filehandle;

    /** @var string */
    private $filenameToServe;

    public function __construct(
        string $filenameToServe,
        string $userFacingFilename,
        array $headers = []
    ) {
        $standardHeaders = [
            'Content-Type' => self::getMimeTypeFromFilename($filenameToServe),
            'Content-Disposition' => 'attachment; filename="' . $userFacingFilename . '"'
        ];

        $this->headers = array_merge($standardHeaders, $headers);

        $this->filehandle = @fopen($filenameToServe, 'r');

        if ($this->filehandle === false) {
            throw new SlimAurynException("Failed to open file [$filenameToServe] for serving.");
        }

        $this->filenameToServe = $filenameToServe;
    }

    public function getStatus() : int
    {
        return 200;
    }

    // if we ever care about not reading the whole file into memory first
    // this function could just emit to output, with appropriate changes in
    // the response mapper
    public function getBody() : string
    {
        $contents = stream_get_contents($this->filehandle);

        if ($contents === false) {
            $message = sprintf(
                "Failed to read contents of [%s] from open filehandle.",
                $this->filenameToServe
            );

            throw new SlimAurynException($message);
        }

        return $contents;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public static function getMimeTypeFromFilename($filename)
    {
        $contentTypesByExtension = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpg',
            'png' => 'image/png',
        ];

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (array_key_exists($extension, $contentTypesByExtension) === false) {
            throw new \Exception("Unknown file type [$extension]");
        }

        return $contentTypesByExtension[$extension];
    }
}
