<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\Patch;
use Params\ValueInput;
use Params\Value\PatchEntry;


use Params\Value\AddPatchEntry;
use Params\Value\CopyPatchEntry;
use Params\Value\MovePatchEntry;
use Params\Value\RemovePatchEntry;
use Params\Value\ReplacePatchEntry;
use Params\Value\TestPatchEntry;
use Params\Value\PatchEntries;

/**
 * @coversNothing
 */
class PatchingTest extends BaseTestCase
{
    private function getPatchEntries($json) : PatchEntries
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error decoding json: " . json_last_error_msg());
        }

        $input = new ValueInput($data);
        $patchRule = new Patch($input, PatchEntry::ALL_OPS);
        $validationResult = $patchRule('foo', null);
        $this->assertNull($validationResult->getProblemMessage());
        $patchEntries = $validationResult->getValue();

        /** @var $patchEntries \Params\Value\PatchEntries */
        return $patchEntries;
    }

    /**
     * @covers \Params\Value\AddPatchEntry
     */
    public function testAddPatchEntry()
    {
        $json = <<< JSON
[
    { "op": "add", "path": "/a/b/c", "value": [ "foo", "bar" ] }
]
JSON;

        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(AddPatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals([ "foo", "bar" ], $addPatchEntry->getValue());
        $this->assertEquals("/a/b/c", $addPatchEntry->getPath());
    }

    /**
     * @covers \Params\Value\CopyPatchEntry
     */
    public function testCopyPatchEntry()
    {
        $json = <<< JSON
[
    { "op": "copy", "from": "/a/b/d", "path": "/a/b/e" }
]
JSON;

        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(CopyPatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals("/a/b/d", $addPatchEntry->getFrom());
        $this->assertEquals("/a/b/e", $addPatchEntry->getPath());
    }

    /**
     * @covers \Params\Value\MovePatchEntry
     */
    public function testMovePatchEntry()
    {
        $json = <<< JSON
[
    { "op": "move", "from": "/a/b/c", "path": "/a/b/d" }
]
JSON;
        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(MovePatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals("/a/b/c", $addPatchEntry->getFrom());
        $this->assertEquals("/a/b/d", $addPatchEntry->getPath());
    }


    /**
     * @covers \Params\Value\RemovePatchEntry
     */
    public function testRemovePatchEntry()
    {
        $json = <<< JSON
[
    { "op": "remove", "path": "/a/b/c" }
]
JSON;

        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(RemovePatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals("/a/b/c", $addPatchEntry->getPath());
    }

    /**
     * @covers \Params\Value\ReplacePatchEntry
     */
    public function testReplacePatchEntry()
    {
        $json = <<< JSON
[
    { "op": "replace", "path": "/a/b/c", "value": 42 }
]
JSON;

        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(ReplacePatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals("/a/b/c", $addPatchEntry->getPath());
        $this->assertEquals(42, $addPatchEntry->getValue());
    }

    /**
     * @covers \Params\Value\TestPatchEntry
     */
    public function testTestPatchEntry()
    {
        $json = <<< JSON
[
    { "op": "test", "path": "/a/b/c", "value": "foo" }
]
JSON;

        $patchEntries = $this->getPatchEntries($json);
        $entries = $patchEntries->getPatchEntries();
        $this->assertCount(1, $entries);

        $this->assertInstanceOf(TestPatchEntry::class, $entries[0]);
        $addPatchEntry = $entries[0];

        $this->assertEquals("/a/b/c", $addPatchEntry->getPath());
        $this->assertEquals("foo", $addPatchEntry->getValue());
    }

    public function provideAsBothObjAndArray()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider provideAsBothObjAndArray
     */
    public function testValidationWorks($asArray)
    {

        $json = <<< JSON
[
    { "op": "test", "path": "/a/b/c", "value": "foo" },
    { "op": "remove", "path": "/a/b/c" },
    { "op": "add", "path": "/a/b/c", "value": [ "foo", "bar" ] },
    { "op": "replace", "path": "/a/b/c", "value": 42 },
    { "op": "move", "from": "/a/b/c", "path": "/a/b/d" },
    { "op": "copy", "from": "/a/b/d", "path": "/a/b/e" }
]

JSON;

        $data = json_decode($json, $asArray);
        $input = new ValueInput($data);
        $patchRule = new Patch($input, PatchEntry::ALL_OPS);
        $validationResult = $patchRule('foo', null);

        $this->assertNull($validationResult->getProblemMessage());
    }

    public function testReplaceMissingValue()
    {
        $data = [[
            "op" => "replace",
            "path" => "/a/b/c",
            "name" => 42 // should be 'value' not 'name'
        ]];

        $input = new ValueInput($data);
        $patchRule = new Patch($input, [PatchEntry::REPLACE]);
        $validationResult = $patchRule('foo', null);

        $this->assertNotNull($validationResult->getProblemMessage());
    }

    public function testCorrectEntryHasError()
    {
        $data = [
            ["op" => "replace", "path" => "/a/b/c", "value" => 42], // correct
            'foobar' // not valid path entry
        ];
        $input = new ValueInput($data);
        $patchRule = new Patch($input, PatchEntry::ALL_OPS);

        $validationResult = $patchRule('foo', null);
        $this->assertContains('Error for entry 1', $validationResult->getProblemMessage());
    }
}
