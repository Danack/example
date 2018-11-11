<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\OrderElement;
use Params\Value\Ordering;

class OrderingTest extends BaseTestCase
{
    public function testBasic()
    {
        $name = 'foo';
        $order = 'asc';

        $orderElment = new OrderElement($name, $order);
        $this->assertEquals($name, $orderElment->getName());
        $this->assertEquals($order, $orderElment->getOrder());

        $ordering = new Ordering([$orderElment]);
        $this->assertEquals([$orderElment], $ordering->getOrderElements());

        $expectedOrderArray = [
            $name => $order
        ];

        $this->assertEquals($expectedOrderArray, $ordering->toOrderArray());
    }
}
