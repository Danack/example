<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

class ImageJobKey
{
    public static function getAbsoluteKeyName() : string
    {
        return str_replace('\\', '', __CLASS__);
    }

    public static function getKeyNameForStatus(string $jobId) : string
    {
        return str_replace('\\', '', __CLASS__) . ':status:' . $jobId;
    }
}
