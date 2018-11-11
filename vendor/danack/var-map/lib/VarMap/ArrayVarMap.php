<?php

namespace VarMap;

use VarMap\Exception\VariableMissingException;

class ArrayVarMap implements VarMap
{
    private $variables;

    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @inheritdoc
     */
    public function get(string $variableName)
    {
        if (!array_key_exists($variableName, $this->variables)) {
            throw VariableMissingException::create($variableName);
        }

        return $this->variables[$variableName];
    }

    /**
     * @inheritdoc
     */
    public function has(string $variableName) : bool
    {
        if (!array_key_exists($variableName, $this->variables)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getWithDefault(string $variableName, $defaultValue)
    {
        if (!array_key_exists($variableName, $this->variables)) {
            return $defaultValue;
        }

        return $this->variables[$variableName];
    }

    /**
     * @inheritdoc
     */
    public function getNames()
    {
        return array_keys($this->variables);
    }
}
