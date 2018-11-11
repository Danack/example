<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class MinLength implements Rule
{
    /** @var int  */
    private $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        if (strlen($value) < $this->minLength) {
            return ValidationResult::errorResult("string for '$name' too short, min chars is " . $this->minLength);
        }
        return ValidationResult::valueResult($value);
    }


    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMinLength($this->minLength);
    }
}
