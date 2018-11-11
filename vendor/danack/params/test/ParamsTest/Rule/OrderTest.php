<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\Order;
use Params\Value\Ordering;

class OrderTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['time', ['time' => Ordering::ASC], false],
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\Order
     */
    public function testValidation($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $validator = new Order($orderParams);
        $validationResult = $validator('foo', $testValue);

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(Ordering::class, $value);
        /** @var $value Ordering */
        $this->assertEquals($expectedOrdering, $value->toOrderArray());
    }
}
