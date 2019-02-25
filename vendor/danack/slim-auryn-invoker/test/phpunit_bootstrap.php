<?php

include_once __DIR__ . "/../vendor/autoload.php";

require __DIR__ . "/fixtures.php";
require __DIR__ . "/../example/functions.php";
require __DIR__ . "/../example/factories.php";

use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Body;
use Slim\Http\Uri;
use Slim\Http\Headers;




function createRequestForTesting() : Request
{
    $headers = [];
    $bodyContent = '';
    $cookies = [];

    $uri = Uri::createFromString('https://user:pass@host:443/path?query');
    $headers = new Headers($headers);
    $serverParams = [];
    $body = new Body(fopen('php://temp', 'r+'));
    $body->write($bodyContent);
    $body->rewind();
    $method = 'GET';
    return new Request($method, $uri, $headers, $cookies, $serverParams, $body);
}