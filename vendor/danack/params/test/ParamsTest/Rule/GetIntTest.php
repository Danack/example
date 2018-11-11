<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetInt;

class GetIntTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\GetString
     */
    public function testMissingGivesError()
    {
        $validator = new GetInt(new ArrayVarMap([]));
        $validationResult = $validator('foo', 'not_used');
        $this->assertNotNull($validationResult->getProblemMessage());
    }

    public function provideTestWorksCases()
    {
        return [
            ['5', 5],
            [5, 5],
        ];
    }

    /**
     * @covers \Params\Rule\GetInt
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';

        $validator = new GetInt(new ArrayVarMap([$variableName => $input]));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    public function provideTestErrorCases()
    {
        return [
            [['foo', null]],
            [['foo', '']],
            [['foo', '6 apples']],
            [['foo', 'banana']],
        ];
    }

    /**
     * @covers \Params\Rule\GetInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($variables)
    {
        $variableName = 'foo';

        $validator = new GetInt(new ArrayVarMap($variables));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
