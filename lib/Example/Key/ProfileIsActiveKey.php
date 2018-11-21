<?php

declare(strict_types=1);

namespace Example\Key;

class ProfileIsActiveKey
{
    public static function getAbsoluteKeyName(int $profile_id) : string
    {
        return __CLASS__ . '_' . $profile_id;
        // return str_replace('\\', '', __CLASS__) . '_' . $profile_id;
    }

    public static function getWildcardKeyName() : string
    {
        return __CLASS__ . '*';
        // return str_replace('\\', '', __CLASS__) . '_*';
    }
}