<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class MaxLength implements Rule
{
    private $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        if (strlen($value) > $this->maxLength) {
            return ValidationResult::errorResult('text label name too long, max chars is ' . $this->maxLength);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMaxLength($this->maxLength);
    }
}
