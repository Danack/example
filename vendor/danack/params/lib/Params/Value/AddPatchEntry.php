<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class AddPatchEntry implements PatchEntry
{
    // Example - { "op": "add", "path": "/a/b/c", "value": [ "foo", "bar" ] }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * AddPatchEntry constructor.
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
        return "add";
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFrom()
    {
        throw new LogicException("Calling 'getFrom' on a AddPatchEntry is meaningless.");
    }

    public function getValue()
    {
        return $this->value;
    }
}
