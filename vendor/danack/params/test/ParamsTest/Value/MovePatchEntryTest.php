<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntry;
use Params\Value\MovePatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class MovePatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\MovePatchEntry
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $from = '/d/e/c';

        $addPatch = new MovePatchEntry($path, $from);

        $this->assertEquals($path, $addPatch->getPath());
        $this->assertEquals($from, $addPatch->getFrom());

        $this->assertEquals(PatchEntry::MOVE, $addPatch->getOp());

        $this->expectException(LogicException::class);
        $addPatch->getValue();
    }
}
