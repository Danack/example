<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\OpenApi\ShouldNeverBeCalledParamDescription;
use Params\Rule\CompleteInput;
use Params\ValueInput;
use ParamsTest\BaseTestCase;

/**
 * @covers \Params\Rule\CompleteInput
 */
class CompleteInputTest extends BaseTestCase
{
    public function testBasic()
    {
        $values = ['foo', 'bar'];
        $input = new ValueInput($values);
        $rule = new CompleteInput($input);

        $result = $rule('foo', 'unused');

        $this->assertEquals($values, $result->getValue());
        $paramDescription = new ShouldNeverBeCalledParamDescription();
        $rule->updateParamDescription($paramDescription);
    }
}
