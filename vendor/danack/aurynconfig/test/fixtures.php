<?php

namespace AurynConfigTest;

interface Foo {}

interface Quux {}

class FooImplementation implements Foo {
    private function __construct() {}
    public static function create()
    {
        return new self();
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