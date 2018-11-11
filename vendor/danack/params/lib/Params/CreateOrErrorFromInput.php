<?php

declare(strict_types=1);

namespace Params;

use VarMap\VarMap;

trait CreateOrErrorFromInput
{
    /**
     * @param VarMap $variableMap
     * @return mixed
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromInput(Input $input)
    {
        $rules = static::getRules($input);

        return Params::createOrError(static::class, $rules);
    }
}
