<?php

declare(strict_types=1);

namespace Params;

class ValueInput implements Input
{
    private $value;

    /**
     * ValueInput constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }
}
