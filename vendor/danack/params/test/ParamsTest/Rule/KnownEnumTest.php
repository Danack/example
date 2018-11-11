<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\Enum;

class KnownEnumValidatorTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['zoq', false, 'zoq'],
            ['Zebranky ', true, null],
            ['12345', false, '12345'],
            [12345, true, null]
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\Enum
     */
    public function testValidation($testValue, $expectError, $expectedValue)
    {
        $knowValues = ['zoq', 'fot', 'pik', '12345'];

        $validator = new Enum($knowValues);
        $validationResult = $validator('foo', $testValue);

        if ($expectError) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
