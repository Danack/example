<?php

declare(strict_types=1);

namespace Example\Service\ExceptionNotifier;

class AirbrakeExceptionNotifier implements ExceptionNotifier
{
    /** @var \Airbrake\Notifier */
    private $airbrakeNotifier;

    /**
     * AibrakeExceptionNotifier constructor.
     * @param \Airbrake\Notifier $airbrakeNotifier
     */
    public function __construct(\Airbrake\Notifier $airbrakeNotifier)
    {
        $this->airbrakeNotifier = $airbrakeNotifier;
    }

    public function notifyCaughtException(\Throwable $t)
    {
        $notice = $this->airbrakeNotifier->buildNotice($t);
        $notice['context']['caught_correctly'] = 'true';
        $this->airbrakeNotifier->sendNotice($notice);
    }

    public function notifyUnhandledException(\Throwable $t)
    {
        $notice = $this->airbrakeNotifier->buildNotice($t);
        $notice['context']['caught_correctly'] = 'false';
        $this->airbrakeNotifier->sendNotice($notice);
    }
}
