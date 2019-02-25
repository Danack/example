<?php

namespace SlimAuryn\Response;

use SlimAuryn\Response\StubResponse;

class CsvDataResponse implements StubResponse
{
    private $headers = [];

    private $statusCode;

    private $dataHeaders;
    private $dataRows;

    private $body;

    public function getStatus() : int
    {
        return $this->statusCode;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * CsvDataResponse constructor.
     * @param array $dataHeaders
     * @param array $dataRows
     * @param string $filename
     * @param array $headers
     * @param int $statusCode
     */
    public function __construct(
        array $dataRows,
        array $dataHeaders = null,
        string $filename = "file.csv",
        array $headers = [],
        int $statusCode = 200
    ) {
        $standardHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->dataHeaders = $dataHeaders;
        $this->dataRows = $dataRows;
        $this->statusCode = $statusCode;

        $this->body = self::strPutCsv($this->dataRows, $this->dataHeaders);
    }

    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * This is only a static method due to PHP not having function autoloading
     * @param array $dataHeaders
     * @param array $dataRows
     * @return string
     */
    public static function strPutCsv(array $dataRows, array $dataHeaders = null)
    {
        # Generate CSV data from array
        $fileHandle = @fopen('php://temp', 'rw'); # don't create a file, attempt
        # to use memory instead

        if ($fileHandle === false) {
            // This should never happen.
            throw new \Exception("Failed to open temp memory for writing.");
        }

        if ($dataHeaders !== null) {
            fputcsv($fileHandle, $dataHeaders);
        }

        foreach ($dataRows as $row) {
            fputcsv($fileHandle, $row);
        }
        rewind($fileHandle);
        $csv = stream_get_contents($fileHandle);
        fclose($fileHandle);

        if ($csv === false) {
            // This should never happen.
            throw new \Exception("Failed to get contents from memory");
        }

        return $csv;
    }
}
