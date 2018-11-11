<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\TestPatchEntry;
use Params\Exception\LogicException;

class TestPatchEntryTest extends BaseTestCase
{
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $replacePatch = new TestPatchEntry($path, $value);

        $this->assertEquals($path, $replacePatch->getPath());
        $this->assertEquals($value, $replacePatch->getValue());

        $this->assertEquals(TestPatchEntry::TEST, $replacePatch->getOp());

        $this->expectException(LogicException::class);
        $replacePatch->getFrom();
    }
}
