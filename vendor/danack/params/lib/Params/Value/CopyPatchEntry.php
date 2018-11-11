<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class CopyPatchEntry implements PatchEntry
{
    // Example - { "op": "copy", "from": "/a/b/c", "path": "/a/b/e" }

    /** @var string */
    private $path;

    /** @var string|null */
    private $from;

    /**
     * CopyPatchEntry constructor.
     * @param string $path
     * @param null|string $from
     */
    public function __construct(string $path, ?string $from)
    {
        $this->path = $path;
        $this->from = $from;
    }

    public function getOp()
    {
        return "copy";
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getValue()
    {
        throw new LogicException("Calling 'getValue' on a CopyPatchEntry is meaningless.");
    }
}
