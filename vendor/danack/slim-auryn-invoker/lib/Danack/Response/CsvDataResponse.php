<?php

namespace Danack\Response;

use Danack\Response\StubResponse;

class CsvDataResponse implements StubResponse
{
    private $headers = [];

    private $statusCode;

    private $dataHeaders;
    private $dataRows;

    private $body;

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * CsvDataResponse constructor.
     * @param $dataHeaders
     * @param $dataRows
     * @param string $filename
     * @param array $headers
     * @param int $statusCode
     */
    public function __construct(
        array $dataRows,
        array $dataHeaders = null,
        $filename = "file.csv",
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

        $this->body = self::strPutCsv($this->dataHeaders, $this->dataRows);
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * This is only a static method due to PHP not having function autoloading
     * @param $dataHeaders
     * @param $dataRows
     * @return string
     */
    public static function strPutCsv($dataHeaders, $dataRows)
    {
        # Generate CSV data from array
        $fh = fopen('php://temp', 'rw'); # don't create a file, attempt
        # to use memory instead

        if ($dataHeaders !== null) {
            fputcsv($fh, $dataHeaders);
        }

        foreach ($dataRows as $row) {
            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $csv;
    }
}
