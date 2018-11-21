<?php

namespace Example\Response;

use Example\Response\Response;


class CsvDataResponse implements Response
{
    private $headers = [];

    private $statusCode;

    private $dataHeaders;
    private $dataRows;

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function __construct($dataHeaders, $dataRows, $filename = "file.csv", array $headers = [], int $statusCode = 200)
    {
        $standardHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $this->headers = array_merge($standardHeaders, $headers);
        $this->dataHeaders = $dataHeaders;
        $this->dataRows = $dataRows;

        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        // TODO - convert to a streaming model when the data >= 1 megabyte.
        return str_putcsv($this->dataHeaders, $this->dataRows);
    }
}
