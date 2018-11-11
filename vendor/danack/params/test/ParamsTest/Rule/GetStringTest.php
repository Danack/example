<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\GetString;

class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\GetString
     */
    public function testMissingGivesError()
    {
        $validator = new GetString(new ArrayVarMap([]));
        $validationResult = $validator('foo', 'not_used');
        $this->assertNotNull($validationResult->getProblemMessage());
    }

    /**
     * @covers \Params\Rule\GetString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $validator = new GetString(new ArrayVarMap([$variableName => $expectedValue]));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
