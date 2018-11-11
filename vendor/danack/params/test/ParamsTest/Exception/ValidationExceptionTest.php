<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;

class ValidationExceptionTest extends BaseTestCase
{
    public function testDoesNotThrow()
    {
        ValidationException::throwIfProblems("Validation problems", []);

    }

    public function testGetting()
    {
        $validationMessages = [
            'foo',
            'bar'
        ];

        $exception = new ValidationException('unit test', $validationMessages);
        $this->assertEquals($validationMessages, $exception->getValidationProblems());
    }

    public function testThrows()
    {
        $problem = "Houston, we have a problem";
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($problem);
        $this->expectExceptionCode(0);
        ValidationException::throwIfProblems(
            "Validation problems",
            [$problem]
        );
    }
}
