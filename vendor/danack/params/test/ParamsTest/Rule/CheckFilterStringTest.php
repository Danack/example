<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\Value\MultipleEnums;
use ParamsTest\BaseTestCase;
use Params\Rule\MultipleEnum;

/**
 * @coversNothing
 */
class CheckFilterSetTest extends BaseTestCase
{
    public function providesKnownFilterCorrect()
    {
        return [
            ['foo', ['foo']],
            ['bar,foo', ['bar', 'foo']],
        ];
    }

    /**
     * @dataProvider providesKnownFilterCorrect
     * @covers \Params\Rule\MultipleEnum
     */
    public function testKnownFilterCorrect($inputString, $expectedResult)
    {
        $validator = new MultipleEnum(['foo', 'bar']);
        $validationResult = $validator('someFilter', $inputString);
        $this->assertNull($validationResult->getProblemMessage());

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \Params\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \Params\Rule\MultipleEnum
     */
    public function testUnknownFilterErrors()
    {
        $expectedValue = 'zot';
        $validator = new MultipleEnum(['foo', 'bar']);
        $validationResult = $validator('someFilter', $expectedValue);
        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
