<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\SkipIfNull;

/**
 * @coversNothing
 */
class SkipIfNullTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [null, true],
            [1, false],
            [0, false],
            [[], false],
            ['banana', false],

        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\SkipIfNull
     */
    public function testValidation($testValue, $expectIsFinalResult)
    {
        $validator = new SkipIfNull();
        $validationResult = $validator('foo', $testValue);
        $this->assertEquals($validationResult->isFinalResult(), $expectIsFinalResult);
    }
}
