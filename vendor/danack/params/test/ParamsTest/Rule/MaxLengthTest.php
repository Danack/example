<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MaxLength;

/**
 * @coversNothing
 */
class MaxLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxLength = 10;
        $underLengthString = str_repeat('a', $maxLength - 1);
        $exactLengthString = str_repeat('a', $maxLength);
        $overLengthString = str_repeat('a', $maxLength + 1);

        return [
            [$maxLength, $underLengthString, false],
            [$maxLength, $exactLengthString, false],
            [$maxLength, $overLengthString, true],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\Rule\MaxLength
     */
    public function testValidation(int $maxLength, string $string, bool $expectError)
    {
        $validator = new MaxLength($maxLength);
        $validationResult = $validator('foo', $string);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
