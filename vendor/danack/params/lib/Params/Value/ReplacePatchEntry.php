<?php

declare(strict_types=1);

namespace Params\Value;

use Params\Exception\LogicException;

class ReplacePatchEntry implements PatchEntry
{
    // Example - { "op": "replace", "path": "/a/b/c", "value": 42 }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * ReplacePatchEntry constructor.
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
        return 'replace';
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFrom()
    {
        throw new LogicException("Calling 'getFrom' on a ReplacePatchEntry is meaningless.");
    }

    public function getValue()
    {
        return $this->value;
    }
}
