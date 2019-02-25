<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntry;
use Params\Value\RemovePatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class RemovePatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\RemovePatchEntry
     */
    public function testBasic()
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

        $this->assertEquals("remove", $removePatch->getOp());
    }

    /**
     * @covers \Params\Value\RemovePatchEntry::getFrom
     */
    public function testGetFromThrows()
    {
        $removePatch = new RemovePatchEntry('/a/b/c');
        $this->expectException(\Params\Exception\LogicException::class);
        $removePatch->getFrom();
    }

    /**
     * @covers \Params\Value\RemovePatchEntry::getValue
     */
    public function testGetValueThrows()
    {
        $removePatch = new RemovePatchEntry('/a/b/c');
        $this->expectException(\Params\Exception\LogicException::class);
        $removePatch->getValue();
    }
}
