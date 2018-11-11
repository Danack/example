<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetOptionalInt;

class GetOptionalIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            // Test value is read as string
            [new ArrayVarMap(['foo' => '5']), 5],
            // Test value is read as int
            [new ArrayVarMap(['foo' => 5]), 5],

            // Test missing param is null
            [new ArrayVarMap([]), null],
        ];
    }

    /**
     * @covers \Params\Rule\GetOptionalInt
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $expectedValue)
    {
        $validator = new GetOptionalInt($varMap);
        $validationResult = $validator('foo', null);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [''],
            ['6 apples'],
            ['banana'],
            ['1.1'],
        ];
    }

    /**
     * @covers \Params\Rule\GetOptionalInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $variableName = 'foo';
        $variables = [$variableName => $inputValue];
        $validator = new GetOptionalInt(new ArrayVarMap($variables));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNotNull($validationResult->getProblemMessage());
        $this->assertNull($validationResult->getValue());
    }
}




