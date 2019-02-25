<?php

declare(strict_types=1);

namespace SlimAurynTest\Foo;

class StandardFoo implements Foo
{
    /** @var bool */
    private $wasConstructedThroughDelegation = false;

    /**
     *
     * @param bool $wasConstructedThroughDelegation
     */
    public function __construct(bool $wasConstructedThroughDelegation = false)
    {
        $this->wasConstructedThroughDelegation = $wasConstructedThroughDelegation;
    }


    /**
     * @return bool
     */
    public function wasConstructedThroughDelegation(): bool
    {
        return $this->wasConstructedThroughDelegation;
    }
}
