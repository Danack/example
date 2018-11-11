<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetOptionalString;

class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $validator = new GetOptionalString(new ArrayVarMap([]));
        $validationResult = $validator('foo', 'not_used');
        $this->assertNull($validationResult->getProblemMessage());
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\Rule\GetOptionalString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $validator = new GetOptionalString(new ArrayVarMap([$variableName => $expectedValue]));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
