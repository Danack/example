<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;
use Params\Value\ReplacePatchEntry;
use Params\Exception\LogicException;

class ReplacePatchEntryTest extends BaseTestCase
{
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $replacePatch = new ReplacePatchEntry($path, $value);

        $this->assertEquals($path, $replacePatch->getPath());
        $this->assertEquals($value, $replacePatch->getValue());

        $this->assertEquals(ReplacePatchEntry::REPLACE, $replacePatch->getOp());

        $this->expectException(LogicException::class);
        $replacePatch->getFrom();
    }
}
