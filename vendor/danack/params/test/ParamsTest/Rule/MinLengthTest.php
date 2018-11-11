<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MinLength;

class MinLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $length = 8;
        $underLengthString = str_repeat('a', $length - 1);
        $exactLengthString = str_repeat('a', $length);
        $overLengthString = str_repeat('a', $length + 1);

        return [
            [$length, $underLengthString, true],
            [$length, $exactLengthString, false],
            [$length, $overLengthString, false],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\Rule\MinLength
     */
    public function testValidation(int $minLength, string $string, bool $expectError)
    {
        $validator = new MinLength($minLength);
        $validationResult = $validator('foo', $string);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
