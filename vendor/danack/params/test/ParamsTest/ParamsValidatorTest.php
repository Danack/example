<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Rule\GetInt;
use Params\Rule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\Rule\AlwaysEndsRule;

class ParamsValidatorTest extends BaseTestCase
{
    public function testMissingRuleThrows()
    {
        $validator = new ParamsValidator();
        $this->expectException(\Params\Exception\ParamsException::class);
        $validator->validate('foobar', []);
    }

    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);

        $rules = [
            new GetInt($arrayVarMap)
        ];

        $validator = new ParamsValidator();

        $value = $validator->validate('foo', $rules);

        $this->assertNull($value);
        $validationProblems = $validator->getValidationProblems();
        $this->assertNotNull($validationProblems);

        $errors = $validationProblems->getValidationProblems();
        $this->assertEquals(1, count($errors));
        $this->assertStringMatchesFormat(GetInt::ERROR_MESSAGE, $errors[0]);
    }


    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            new GetInt($arrayVarMap),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        ];

        $validator = new ParamsValidator();

        $value = $validator->validate('foo', $rules);

        $this->assertEquals($finalValue, $value);
        $this->assertEmpty($validator->getValidationProblems());
    }
}
