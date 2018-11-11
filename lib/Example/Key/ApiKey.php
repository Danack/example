<?php

namespace Example\Key;

class ApiKey
{
    public static function getAbsoluteKeyName(string $prefix) : string
    {
        return str_replace('\\', '', __CLASS__) . '_' . $prefix;
    }
}
