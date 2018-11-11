<?php

declare(strict_types=1);

namespace Example\ApiController;

use Example\Exception\DebuggingUncaughtException;
use Example\Exception\DebuggingCaughtException;

class Debug
{
    public function testUncaughtException()
    {
        throw new DebuggingUncaughtException("Hello, I am a test exception that won't be caught by the application.");
    }


    public function testCaughtException()
    {
        throw new DebuggingCaughtException("Hello, I am a test exception that will be caught by the application.");
    }
}
