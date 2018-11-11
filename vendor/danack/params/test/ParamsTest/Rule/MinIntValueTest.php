<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MinIntValue;

class MinIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $underValue, true],
            [$minValue, $exactValue, false],
            [$minValue, $overValue, false],

            // TODO - think about these cases.
            [$minValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMinIntValueCases
     * @covers \Params\Rule\MinIntValue
     */
    public function testValidation(int $minValue, string $inputValue, bool $expectError)
    {
        $validator = new MinIntValue($minValue);
        $validationResult = $validator('foo', $inputValue);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
