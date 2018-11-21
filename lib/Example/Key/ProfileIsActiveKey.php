<?php

declare(strict_types=1);

namespace Example\Key;

class ProfileIsActiveKey
{
    public static function getAbsoluteKeyName(int $profile_id) : string
    {
        return str_replace('\\', '', __CLASS__) . '_' . $profile_id;
    }

    public static function getWildcardKeyName() : string
    {
        return str_replace('\\', '', __CLASS__) . '_*';
    }
}
