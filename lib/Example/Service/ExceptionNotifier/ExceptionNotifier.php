<?php

declare(strict_types=1);

namespace Example\Service\ExceptionNotifier;

interface ExceptionNotifier
{
    /**
     * Log that an exception that was correctly mapped to a nicely formatted
     * response was caught
     * @param \Throwable $t
     * @return mixed
     */
    public function notifyCaughtException(\Throwable $t);

    /**
     * Log that an exception that was not mapped to a nicely formatted response
     * was caught by slim
     * @param \Throwable $t
     * @return mixed
     */
    public function notifyUnhandledException(\Throwable $t);
}
