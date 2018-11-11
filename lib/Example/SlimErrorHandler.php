<?php

declare(strict_types=1);

namespace Example;

use Slim\Container;
use Example\Service\ExceptionNotifier\ExceptionNotifier;

class SlimErrorHandler
{
    /** @var Container  */
    private $c;

    /** @var ExceptionNotifier */
    private $exceptionNotifier;

    public function __construct(\Slim\Container $c, ExceptionNotifier $exceptionNotifier)
    {
        $this->c = $c;
        $this->exceptionNotifier = $exceptionNotifier;
    }

    public function __invoke($request, $response, $exception)
    {
        $text = "";
        /** @var $exception \Exception */
        $currentException = $exception;

        do {
            $text .= get_class($currentException) . ":" . $currentException->getMessage() . "\n\n";
            $text .= $currentException->getTraceAsString();
        } while (($currentException = $currentException->getPrevious()) !== null);

        error_log($text);

        try {
            $this->exceptionNotifier->notifyUnhandledException($exception);
        }
        catch (\Exception $e) {
            error_log("Exception pushing notifications through airbrake: " . $e->getMessage());
        }

        $response = [
            'status' => 'error',
            'message' => $exception->getMessage(),
            'details' => 'Unhandled exception type ' . get_class($exception)
        ];

        return $this->c['response']->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(\json_encode($response, JSON_PRETTY_PRINT));
    }
}
