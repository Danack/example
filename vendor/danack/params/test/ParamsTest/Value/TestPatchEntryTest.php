<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\TestPatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class TestPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\TestPatchEntry
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $patch = new TestPatchEntry($path, $value);

        $this->assertEquals($path, $patch->getPath());
        $this->assertEquals($value, $patch->getValue());

        $this->assertEquals(TestPatchEntry::TEST, $patch->getOp());

        $this->expectException(LogicException::class);
        $patch->getFrom();

        $this->assertEquals('test', $patch->getOp());
    }


    /**
     * @covers \Params\Value\TestPatchEntry::getFrom
     */
    public function testGetFromThrows()
    {
        $patch = new TestPatchEntry('/a/b/c', 5);
        $this->expectException(\Params\Exception\LogicException::class);
        $patch->getFrom();
    }
}
