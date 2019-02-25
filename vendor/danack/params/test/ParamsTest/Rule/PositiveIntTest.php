<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\PositiveInt;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['5', 5, false],
            ['5.5', null, true], // not an int
            ['banana', null, true], // not an int
            ['0', 0, false], // close enough
            [PositiveInt::MAX_SANE_VALUE + 1 , null, true],
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE, false],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1, false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\PositiveInt
     */
    public function testValidation($testValue, $expectedResult, $expectError)
    {
        $validator = new PositiveInt();
        $validationResult = $validator('foo', $testValue);
        if ($expectError == true) {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNull($validationResult->getProblemMessage());
            $this->assertEquals($validationResult->getValue(), $expectedResult);
        }
    }
}
