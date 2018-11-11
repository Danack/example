<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MaxIntValue;

class MaxIntValueValidatorTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, $underValue, false],
            [$maxValue, $exactValue, false],
            [$maxValue, $overValue, true],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\Rule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $string, bool $expectError)
    {
        $validator = new MaxIntValue($maxValue);
        $validationResult = $validator('foo', $string);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
