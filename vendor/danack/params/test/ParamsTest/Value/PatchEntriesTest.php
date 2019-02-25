<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntries;
use Params\Value\AddPatchEntry;
use Params\Value\TestPatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class PatchEntriesTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\PatchEntries
     */
    public function testBasic()
    {
        $path = '/a/b/c';
        $value = 5;

        $addPatch = new AddPatchEntry($path, $value);
        $testPatch = new TestPatchEntry('/a/b/c', 5);


        $patchEntries = new PatchEntries(...[
            $addPatch,
            $testPatch
        ]);


        $patchEntries = $patchEntries->getPatchEntries();

        $this->assertCount(2, $patchEntries);
        $this->assertEquals($addPatch, $patchEntries[0]);
        $this->assertEquals($testPatch, $patchEntries[1]);
    }
}
