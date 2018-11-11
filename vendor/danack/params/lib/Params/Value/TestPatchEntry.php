<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class TestPatchEntry implements PatchEntry
{
    // Example - { "op": "test", "path": "/a/b/c", "value": "foo" }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * TestPatchEntry constructor.
     * @param string $path
     * @param mixed $value
     */
    public function __construct(string $path, $value)
    {
        $this->path = $path;
        $this->value = $value;
    }

    public function getOp()
    {
        return "test";
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFrom()
    {
        throw new LogicException("Calling 'getFrom' on a TestPatchEntry is meaningless.");
    }

    public function getValue()
    {
        return $this->value;
    }
}
