<?php

declare(strict_types=1);

/**
 * This is a set of functions that map exceptions that are otherwise uncaught into
 * acceptable responses that will be seen by the public
 */

use Psr\Http\Message\ResponseInterface;

function fillResponseData(ResponseInterface $response, array $data, int $statusCode)
{
    $builtResponse = new \Example\Response\DataResponse($data, [], $statusCode);

    $response = $response->withStatus($builtResponse->getStatus());
    foreach ($builtResponse->getHeaders() as $key => $value) {
        /** @var $response \Psr\Http\Message\ResponseInterface */
        $response = $response->withAddedHeader($key, $value);
    }

    $response->getBody()->write($builtResponse->getBody());

    return $response;
}


function paramsValidationExceptionMapper(\Params\Exception\ValidationException $ve, ResponseInterface $response)
{
    $data = [];
    $data['status'] = 'There were validation errors';
    $data['errors'] = $ve->getValidationProblems();

    $response = fillResponseData($response, $data, 400);

    return $response;
}


function errorLogException(\Throwable $exception)
{
    $currentException = $exception;
    $text = "Exception of type " . get_class($exception) . " caught:";

    do {
        $text .= get_class($currentException) . ":" . $currentException->getMessage() . "\n\n";
        $text .= $currentException->getTraceAsString();
    } while (($currentException = $currentException->getPrevious()) !== null);

    error_log($text);
}

function pdoExceptionMapper(\PDOException $pdoe, ResponseInterface $response)
{
    errorLogException($pdoe);

    $statusMessage = 'Unknown error';
    $knownStatusCodes = [
        1045 => 'Configuration error, could not connect to DB.', // Config error
        42000 => 'Database error, could not query', // SQL syntax
    ];

    if (array_key_exists($pdoe->getCode(), $knownStatusCodes) === true) {
        $statusMessage = $knownStatusCodes[$pdoe->getCode()];
    }

    $data = [];
    $data['status'] = $statusMessage;
    $data['errors'] = 'PDOException code is ' . $pdoe->getCode();

    $response = fillResponseData($response, $data, 500);

    return $response;
}

function debuggingCaughtExceptionExceptionMapper(
    \Example\Exception\DebuggingCaughtException $pdoe,
    ResponseInterface $response
) {
    errorLogException($pdoe);

    $data = [];
    $data['status'] = "Correctly caught DebuggingCaughtException";

    $response = fillResponseData($response, $data, 500);

    return $response;
}

