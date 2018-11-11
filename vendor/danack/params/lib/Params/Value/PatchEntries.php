<?php

declare(strict_types=1);

namespace Params\Value;

class PatchEntries
{
    /** @var PatchEntry[] */
    private $patchEntries;

    /**
     * Patch constructor.
     */
    public function __construct(PatchEntry ...$patchEntries)
    {
        $this->patchEntries = $patchEntries;
    }

    /**
     * @return PatchEntry[]
     */
    public function getPatchEntries(): array
    {
        return $this->patchEntries;
    }
}
