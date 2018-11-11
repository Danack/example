<?php

declare(strict_types=1);

namespace Params;

use VarMap\VarMap;

trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return object|static
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap)
    {
        $rules = static::getRules($variableMap);
        return Params::create(static::class, $rules);
    }
}
