<?php

declare(strict_types=1);

namespace Example\Service;

use Airbrake\Notifier as AirbrakeNotifier;
use Psr\Http\Message\ResponseInterface;
use Example\Service\ExceptionNotifier\ExceptionNotifier;

class ExceptionNotifierWrapper
{
    /** @var ExceptionNotifier */
    private $exceptionNotifier;

    /** @var array List of exceptions that are _NOT_ pushed to be logged */
    private $unloggedExceptionTypes = [];

    public function __construct(ExceptionNotifier $notifier)
    {
        $this->exceptionNotifier = $notifier;
    }

    public function wrapExceptionMapper(callable $exceptionMapper)
    {
        return function (
            \Throwable $t,
            ResponseInterface $response
        ) use ($exceptionMapper) {

            $pushException = true;

            foreach ($this->unloggedExceptionTypes as $unloggedExceptionType) {
                if ($t instanceof $unloggedExceptionType) {
                    $pushException = false;
                    break;
                }
            }

            if ($pushException === true) {
                $this->exceptionNotifier->notifyCaughtException($t);
            }

            return $exceptionMapper($t, $response);
        };
    }
}
