<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class Trim implements Rule
{
    public function __invoke(string $name, $value): ValidationResult
    {
        return ValidationResult::valueResult(trim($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // Does nothing?
    }
}
