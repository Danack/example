<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;

class MultipleEnumsTest extends BaseTestCase
{
    public function testDoesNotThrow()
    {
        $values = [
            'foo',
            'bar'
        ];

        $multipleEnums = new MultipleEnums($values);

        $this->assertEquals($values, $multipleEnums->getValues());
    }
}
