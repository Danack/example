<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\ValidationErrors;
use ParamsTest\BaseTestCase;

class ValidationErrorsTest extends BaseTestCase
{
    public function testIteration()
    {
        $errorStrings = [
            'error 1',
            'error 2',
            'error 3',
        ];

        $validationErrors = new ValidationErrors($errorStrings);

        $iteratedErrors = [];

        foreach ($validationErrors as $error) {
            $iteratedErrors[] = $error;
        }

        $this->assertEquals($errorStrings, $iteratedErrors);
    }
}
