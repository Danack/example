<?php

namespace Example\Response;

use Example\Response\Response;

class HtmlNoCacheResponse implements Response
{
    private $body;

    private $status;

    private $headers = [];

    public function getStatus()
    {
        return $this->status;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function __construct($html, int $status, array $headers = [])
    {
        $this->status = $status;
        $standardHeaders = [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store',
        ];


// TODO - we could lock down the javascript and other resources that can be run on
// a site, via a CSP header, like one of the following.
//
// Content-Security-Policy: default-src 'self'; ...; report-uri /my_amazing_csp_report_parser;
// Content-Security-Policy: script-src 'unsafe-inline';
// Content-Security-Policy-Report-Only: default-src 'self'; ...; report-uri /my_amazing_csp_report_parser;
// Content-Security-Policy: default-src 'none'; script-src 'self' 'unsafe-inline' www.google-analytics.com; img-src www.google-analytics.com;

    // Cache-Control: must-revalidate
    // Cache-Control: no-cache
    // Cache-Control: no-store
    // Cache-Control: no-transform
    // Cache-Control: public
    // Cache-Control: private
    // Cache-Control: proxy-revalidate
    // Cache-Control: max-age=<seconds>
    // Cache-Control: s-maxage=<seconds>
        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $html;
    }

    public function getBody()
    {
        return $this->body;
    }
}
