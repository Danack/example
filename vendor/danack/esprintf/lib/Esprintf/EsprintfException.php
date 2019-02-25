<?php

declare(strict_types=1);

namespace Esprintf;

class EsprintfException extends \Exception
{
    const KEY_IS_NOT_STRING      = "escape key at position[%d] is not a string, instead [%s]";

    const UNKNOWN_ESCAPER_STRING = "Couldn't find escaper type for search string [%s]";

    public static function fromKeyIsNotString(int $position, $key)
    {
        $message = sprintf(
            self::KEY_IS_NOT_STRING,
            $position,
            var_export($key, true)
        );

        return new self($message);
    }


    public static function fromUnknownSearchString($searchString)
    {
        $message = sprintf(
            self::UNKNOWN_ESCAPER_STRING,
            substr($searchString, 0, 20)
        );

        return new self($message);
    }
}
