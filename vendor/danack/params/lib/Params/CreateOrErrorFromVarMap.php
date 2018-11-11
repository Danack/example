<?php

declare(strict_types=1);

namespace Params;

use VarMap\VarMap;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return object|static
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $namedRules = static::getRules($variableMap);

        return Params::createOrError(static::class, $namedRules);
    }
}
