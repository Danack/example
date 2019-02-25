<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\NotNull;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\NotNull
     */
    public function testValidation()
    {
        $validator = new NotNull();
        $validationResult = $validator('foo', null);
        $this->assertNotNull($validationResult->getProblemMessage());

        $validator = new NotNull();
        $validationResult = $validator('foo', 5);
        $this->assertNull($validationResult->getProblemMessage());
    }
}
