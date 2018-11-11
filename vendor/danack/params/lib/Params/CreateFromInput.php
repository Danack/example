<?php

declare(strict_types=1);

namespace Params;

use Params\Params;
use VarMap\VarMap;

trait CreateFromInput
{
    /**
     * @param VarMap $variableMap
     * @return object|static
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromInput(Input $input)
    {
        $rules = static::getRules($input);

        return Params::create(static::class, $rules);
    }
}
