<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\Rule\IntegerInput;
use ParamsTest\BaseTestCase;


class IntegerInputValidatorTest extends BaseTestCase
{
    public function provideIntValueWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            [IntegerInput::MAX_SANE_VALUE, IntegerInput::MAX_SANE_VALUE]
        ];
    }

    /**
     * @dataProvider provideIntValueWorksCases
     * @covers \Params\Rule\IntegerInput
     */
    public function testValidationWorks(string $inputValue, int $expectedValue)
    {
        $validator = new IntegerInput();
        $validationResult = $validator('foo', $inputValue);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideMinIntValueErrorsCases()
    {
        return [
            // todo - we should test the exact error.
            ['-5'],
            ['5.5'],
            ['banana'],
            [''],
            [(string)(IntegerInput::MAX_SANE_VALUE + 1)]
        ];
    }

    /**
     * @dataProvider provideMinIntValueErrorsCases
     * @covers \Params\Rule\IntegerInput
     */
    public function testValidationErrors(string $inputValue)
    {
        $validator = new IntegerInput();
        $validationResult = $validator('foo', $inputValue);
        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
