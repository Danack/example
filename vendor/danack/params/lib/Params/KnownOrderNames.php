<?php

declare(strict_types=1);

namespace Params;

/**
 * Interface KnownOrderNames
 *
 * Implementations must return an array of strings, that list
 * the known items the parameter can be ordered by.
 */
interface KnownOrderNames
{
    /** @return string[] */
    public function getKnownOrderNames();
}
