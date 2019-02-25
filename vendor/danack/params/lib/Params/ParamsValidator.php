<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\ParamsException;
use Params\ValidationErrors;

/**
 * Class ParamsValidator
 *
 * Validates an input parameter according to a set of rules.
 * If there are any errors, they will be stored in this object,
 * and can be retrieved via the method ParamsValidator::getValidationProblems
 */
class ParamsValidator
{
    /**
     * @var array
     */
    private $validationProblems = [];

    public function __construct()
    {
        $this->validationProblems = [];
    }

    /**
     * @param string $name
     * @param \Params\Rule[] $rules
     * @return mixed
     * @throws ValidationException
     * @throws ParamsException
     */
    public function validate(string $name, array $rules)
    {
        if (count($rules) === 0) {
            throw new ParamsException('Rules for validating ' . $name . ' are not set.');
        }

        $value = null;
        foreach ($rules as $rule) {
            $validationResult = $rule($name, $value);
            /** @var $validationResult \Params\ValidationResult */
            if (($validationProblem = $validationResult->getProblemMessage()) != null) {
                $this->validationProblems[] = $validationProblem;
                return null;
            }

            $value = $validationResult->getValue();
            if ($validationResult->isFinalResult() === true) {
                break;
            }
        }

        return $value;
    }


    public function getValidationProblems(): ?ValidationErrors
    {
        if (count($this->validationProblems) !== 0) {
            return new ValidationErrors($this->validationProblems);
        }

        return null;
    }
}
