<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\Trim;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\Trim
     */
    public function testValidation()
    {
        $validator = new Trim();
        $validationResult = $validator('foo', ' bar ');
        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
