<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

/**
 * @TODO - is there any point to this rule?
 */
class NotNull implements Rule
{
    public function __invoke(string $name, $value): ValidationResult
    {
        if ($value === null) {
            return ValidationResult::errorResult("null is not allowed for '$name'.");
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
    }
}
