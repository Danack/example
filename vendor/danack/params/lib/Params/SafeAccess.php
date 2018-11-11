<?php

namespace Params;

/**
 * @codeCoverageIgnore
 */
trait SafeAccess
{
    public function __set($name, $value)
    {
        throw new \Exception("Property [$name] doesn't exist for class [".get_class($this)."] so can't set it");
    }

    public function __get($name)
    {
        throw new \Exception("Property [$name] doesn't exist for class [".get_class($this)."] so can't get it");
    }
}
