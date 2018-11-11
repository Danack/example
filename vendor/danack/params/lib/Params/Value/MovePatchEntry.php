<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class MovePatchEntry implements PatchEntry
{
    // Example - { "op": "move", "from": "/a/b/c", "path": "/a/b/d" }

    /** @var string */
    private $path;

    /** @var string|null */
    private $from;

    /**
     * MovePatchEntry constructor.
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
        return "move";
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
        throw new LogicException("Calling 'getValue' on a MovePatchEntry is meaningless.");
    }
}
