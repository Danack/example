<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

/**
 * Class ValidCharacters
 *
 * Checks that an input string contains only valid characters.
 * Flags used for preg_match are xu
 *
 */
class ValidCharacters implements Rule
{
    /** @var string */
    private $patternValidCharacters;

    const INVALID_CHAR_MESSAGE = "Invalid character at position %d. Allowed characters are %s";

    public function __construct(string $patternValidCharacters)
    {
        $this->patternValidCharacters = $patternValidCharacters;
    }

    public function __invoke(string $name, $value): ValidationResult
    {

        $patternInvalidCharacters = "/[^" . $this->patternValidCharacters . "]+/xu";
        $matches = [];
        $count = preg_match($patternInvalidCharacters, $value, $matches, PREG_OFFSET_CAPTURE);

        if ($count) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                self::INVALID_CHAR_MESSAGE,
                $badCharPosition,
                $this->patternValidCharacters
            );
            return ValidationResult::errorResult($message);
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setPattern($this->patternValidCharacters);
    }
}
