<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;

/**
 * @coversNothing
 */
class MultipleEnumsTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\MultipleEnums
     */
    public function testBasic()
    {
        $values = [
            'foo',
            'bar'
        ];

        $multipleEnums = new MultipleEnums($values);

        $this->assertEquals($values, $multipleEnums->getValues());
    }
}
