<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MultipleEnum;
use Params\Value\MultipleEnums;

/**
 * @coversNothing
 */
class MultipleEnumTest extends BaseTestCase
{
    public function provideMultipleEnumCases()
    {
        return [
            ['foo,', ['foo']],
            [',,,,,foo,', ['foo']],
        ];
    }

    /**
     * @dataProvider provideMultipleEnumCases
     * @covers \Params\Rule\MultipleEnum
     */
    public function testMultipleEnum_emptySegments($input, $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $result = $enumRule('unused', $input);

        $this->assertNull($result->getProblemMessage());
        $value = $result->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);
        $this->assertEquals($expectedOutput, $value->getValues());
    }

    // TODO - these appear to be duplicate tests.
    public function provideTestCases()
    {
        return [
            ['time', ['time'], false],
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\MultipleEnum
     */
    public function testValidation($testValue, $expectedFilters, $expectError)
    {
        $validator = new MultipleEnum(['time', 'distance']);
        $validationResult = $validator('foo', $testValue);

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(\Params\Value\MultipleEnums::class, $value);

        /** @var $value \Params\Value\MultipleEnums */
        $this->assertEquals($expectedFilters, $value->getValues());
    }
}
