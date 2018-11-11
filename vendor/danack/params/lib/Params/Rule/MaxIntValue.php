<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class MaxIntValue implements Rule
{
    /** @var int  */
    private $maxValue;

    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        $value = intval($value);
        if ($value > $this->maxValue) {
            return ValidationResult::errorResult("Value too large. Max allowed is " . $this->maxValue);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
