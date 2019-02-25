<?php

declare(strict_types=1);


namespace SlimAurynTest\Foo;


interface Foo
{
    public function wasConstructedThroughDelegation(): bool;
}