<?php

namespace VarMap;

interface VarMap
{
    /**
     * @param string $name
     * @return mixed
     * @throws \VarMap\Exception\VariableMissingException
     */
    public function get(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getWithDefault(string $name, $defaultValue);

    /**
     * Returns a list of names of all the entries in the InputMap
     *
     * @return string[]
     */
    public function getNames();
}
