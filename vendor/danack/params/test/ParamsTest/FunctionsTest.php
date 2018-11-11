<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\Functions;
use Params\Value\Ordering;

class FunctionsTest extends BaseTestCase
{
    public function providesNormaliseOrderParameter()
    {
        return [
            ['foo', 'foo', Ordering::ASC],
            ['+foo', 'foo', Ordering::ASC],
            ['-foo', 'foo', Ordering::DESC],
        ];
    }

    /**
     * @dataProvider providesNormaliseOrderParameter
     */
    public function testNormaliseOrderParameter($input, $expectedName, $expectedOrder)
    {
        list($name, $order) = Functions::normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }


    public function testCheckOnlyDigits()
    {
        // An integer gets short circuited
        $errorMsg = Functions::check_only_digits('Foo', 12345);
        $this->assertNull($errorMsg);

        // Correct string passes through
        $errorMsg = Functions::check_only_digits('Foo', '12345');
        $this->assertNull($errorMsg);

        // Incorrect string passes through
        $errorMsg = Functions::check_only_digits('Foo', '123X45');
        $this->assertStringMatchesFormat("%sposition 3%s", $errorMsg);
        $this->assertStringMatchesFormat("%sFoo%s", $errorMsg);
    }

    public function testArrayValueExists()
    {
        $values = [
            '1',
            '2',
            '3'
        ];

        $foundExactType = Functions::array_value_exists($values, '2');
        $this->assertTrue($foundExactType);

        $foundJuggledType = Functions::array_value_exists($values, 2);
        $this->assertFalse($foundJuggledType);
    }
}
