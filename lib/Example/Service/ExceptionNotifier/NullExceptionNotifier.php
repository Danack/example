<?php

declare(strict_types=1);

namespace Example\Service\ExceptionNotifier;

class NullExceptionNotifier implements ExceptionNotifier
{
    public function notifyCaughtException(\Throwable $t)
    {
    }

    public function notifyUnhandledException(\Throwable $t)
    {
    }
}
