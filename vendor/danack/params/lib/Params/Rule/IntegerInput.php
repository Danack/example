<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class IntegerInput implements Rule
{
    const MAX_SANE_VALUE = 999999999999999;

    public function __invoke(string $name, $value) : ValidationResult
    {
        // TODO - check is null
        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                $message = sprintf(
                    "Value for %s is an empty string - should be an integer.",
                    $name
                );

                return ValidationResult::errorResult($message);
            }

            // check string length is not zero length.
            $match = preg_match("/[^0-9]+/", $value);

            if ($match !== 0) {
                $message = sprintf(
                    "Value for %s must contain only digits.",
                    $name
                );

                return ValidationResult::errorResult($message);
            }
        }

        $maxSaneLength = strlen((string)(self::MAX_SANE_VALUE));

        if (strlen((string)$value) > $maxSaneLength) {
            $message = sprintf(
                "Value for %s too long, max %s digits",
                $name,
                $maxSaneLength
            );

            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult(intval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // todo - this seems like a not needed rule.
    }
}
