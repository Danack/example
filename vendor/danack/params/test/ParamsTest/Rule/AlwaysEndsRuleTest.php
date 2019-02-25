<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\AlwaysEndsRule;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\AlwaysEndsRule
     */
    public function testUnknownFilterErrors()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);

        $result = $rule('foo', 5);

        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
        $this->assertNull($result->getProblemMessage());
    }
}
