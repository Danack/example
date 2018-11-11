<?php

namespace VarMap\Exception;

/**
 * Exception thrown when someone tries to read a variable that is
 * not available in the InputMap
 */
class VariableMissingException extends VarMapException
{
    public static function create($variableName)
    {
        $message = "Variable [$variableName] is not available";
        return new self($message);
    }
}
