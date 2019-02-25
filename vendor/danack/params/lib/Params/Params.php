<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\RulesEmptyException;

/**
 * Class Params
 *
 * Validates multiple parameters at once, each according to their
 * own set of rules.
 *
 * Any validation problem will cause a ValidationException to be thrown.
 *
 */
class Params
{
    /**
     * @param array $namedRules
     * @return array
     * @throws ValidationException
     * @throws RulesEmptyException
     */
    public static function validate($namedRules)
    {
        $values = [];
        $validationProblems = [];

        foreach ($namedRules as $name => $rules) {
            if (count($rules) === 0) {
                throw new RulesEmptyException('Rules for validating ' . $name . ' are not set.');
            }

            $value = null;
            foreach ($rules as $rule) {
                $validationResult = $rule($name, $value);
                /** @var $validationResult \Params\ValidationResult */
                if (($validationProblem = $validationResult->getProblemMessage()) != null) {
                    $validationProblems[] = $validationProblem;
                    break;
                }
                $value = $validationResult->getValue();
                if ($validationResult->isFinalResult() === true) {
                    break;
                }
            }
            $values[] = $value;
        }

        ValidationException::throwIfProblems("Validation problems", $validationProblems);

        return $values;
    }

    /**
     * @param string $classname
     * @param array $namedRules
     * @return object
     * @throws RulesEmptyException
     * @throws ValidationException
     */
    public static function create($classname, $namedRules)
    {
        $params = self::validate($namedRules);
        $reflection_class = new \ReflectionClass($classname);
        return $reflection_class->newInstanceArgs($params);
    }

    /**
     * @param string $classname
     * @param array $namedRules
     * @return mixed -  [object|null, ValidationErrors|null]
     * @throws Exception\ParamsException
     * @throws ValidationException
     */
    public static function createOrError($classname, $namedRules)
    {
        $validator = new ParamsValidator();
        $params = [];
        foreach ($namedRules as $name => $rules) {
            $params[] = $validator->validate($name, $rules);
        }

        $validationErrors = $validator->getValidationProblems();
        if ($validationErrors !== null) {
            return [null, $validationErrors];
        }

        $reflection_class = new \ReflectionClass($classname);
        return [$reflection_class->newInstanceArgs($params), null];
    }
}
