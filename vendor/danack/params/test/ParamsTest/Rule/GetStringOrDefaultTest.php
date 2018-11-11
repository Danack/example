<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetStringOrDefault;

class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],
            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\GetStringOrDefault
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $validator = new GetStringOrDefault($default, $varMap);
        $validationResult = $validator('foo', null);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
