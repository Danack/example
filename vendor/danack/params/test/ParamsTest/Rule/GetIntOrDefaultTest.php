<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\Rule\GetIntOrDefault;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetStringOrDefault;

class GetIntOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [

            // Test value is read as string
            [new ArrayVarMap(['foo' => '5']), 'john', 5],
            // Test value is read as int
            [new ArrayVarMap(['foo' => 5]), 'john', 5],

            // Test default is used as string
            [new ArrayVarMap([]), '5', 5],

            // Test default is used as int
            [new ArrayVarMap([]), 5, 5],

            // Test default is used as null
            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @covers \Params\Rule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $validator = new GetIntOrDefault($default, $varMap);
        $validationResult = $validator('foo', null);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [null],
            [''],
            ['6 apples'],
            ['banana'],
            ['1.1'],
        ];
    }

    /**
     * @covers \Params\Rule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];


        $validator = new GetIntOrDefault($default, new ArrayVarMap($variables));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNotNull($validationResult->getProblemMessage());
    }
}




