<?php

declare(strict_types=1);

namespace SlimAurynTest;

interface Foo {}

interface Quux {}

class FooImplementation implements Foo {
    private function __construct() {}
    public static function create()
    {
        return new self();
    }
}

class FooPublicConstructor implements Foo {

}


interface StringValue {
    public function getString(): string;
}

class BasicStringValue implements StringValue {

    /** @var string */
    private $stringValue;

    /**
     * @param string $stringValue
     */
    public function __construct(string $stringValue)
    {
        $this->stringValue = $stringValue;
    }

    public function getString(): string
    {
        return $this->stringValue;
    }
}



class MutableStringValue implements StringValue {

    /** @var string */
    private $stringValue;

    /**
     * @param string $stringValue
     */
    public function __construct(string $stringValue)
    {
        $this->stringValue = $stringValue;
    }

    public function getString(): string
    {
        return $this->stringValue;
    }

    /**
     * @param string $stringValue
     */
    public function setStringValue(string $stringValue): void
    {
        $this->stringValue = $stringValue;
    }
}


class Bar {
    private function __construct() {}
    public static function create()
    {
        return new self();
    }
}

class QuuxImplementation implements Quux {
    private function __construct() {}
    public static function create()
    {
        return new self();
    }
}

class MappedException extends \Exception
{

}

class UnmappedException extends \Exception
{

}



