<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntry;
use Params\Value\RemovePatchEntry;
use Params\Exception\LogicException;

class RemovePatchEntryTest extends BaseTestCase
{
    public function testFoo()
    {
        $path = '/a/b/c';
        $removePatch = new RemovePatchEntry($path);

        $this->assertEquals($path, $removePatch->getPath());
        $this->assertEquals(PatchEntry::REMOVE, $removePatch->getOp());

        try {
            $removePatch->getFrom();
            $this->fail('getFrom failed to throw LogicException');
        }
        catch (LogicException $le) {
            $this->assertTrue(true);
        }

        $this->expectException(LogicException::class);
        $removePatch->getValue();
    }
}
