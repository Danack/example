<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class RemovePatchEntry implements PatchEntry
{

    // Example - { "op": "remove", "path": "/a/b/c" }

    /** @var string */
    private $path;

    /**
     * RemovePatchEntry constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getOp()
    {
        return "remove";
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
        throw new LogicException("Calling 'getValue' on a RemovePatchEntry is meaningless.");
    }
}
