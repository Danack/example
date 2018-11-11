<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class AlwaysEndsRule implements Rule
{
    private $finalResult;

    public function __construct($finalResult)
    {
        $this->finalResult = $finalResult;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        return ValidationResult::finalValueResult($this->finalResult);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // Does nothing.
    }
}
