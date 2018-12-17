<?php

namespace Example\Queue;

class PrintUrlToPdfJobJobKey
{
    public static function getKeyPrefix()
    {
        return str_replace('\\', '', __CLASS__) . '_';
    }
}
