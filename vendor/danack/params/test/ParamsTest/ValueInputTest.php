<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Exception\ValidationException;
use Params\Rule\GetInt;
use Params\Rule\GetStringOrDefault;
use Params\Rule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;
use Params\Rule\AlwaysEndsRule;
use Params\Rule\MaxIntValue;
use Params\Rule\AlwaysErrorsRule;
use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ValueInput;

/**
 * @coversNothing
 */
class ValueInputTest extends BaseTestCase
{
    /**
     * @covers \Params\ValueInput
     */
    public function testMissingRuleThrows()
    {
        $value = ['abc', 123];
        $valueInput = new ValueInput($value);
        $this->assertEquals($value, $valueInput->get());
    }
}
