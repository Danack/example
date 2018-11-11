<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntry;
use Params\Value\CopyPatchEntry;
use Params\Exception\LogicException;

class CopyPatchEntryTest extends BaseTestCase
{
    public function testFoo()
    {
        $path = '/a/b/c';
        $from = '/d/e/c';

        $addPatch = new CopyPatchEntry($path, $from);

        $this->assertEquals($path, $addPatch->getPath());
        $this->assertEquals($from, $addPatch->getFrom());

        $this->assertEquals(PatchEntry::COPY, $addPatch->getOp());

        $this->expectException(LogicException::class);
        $addPatch->getValue();
    }
}
