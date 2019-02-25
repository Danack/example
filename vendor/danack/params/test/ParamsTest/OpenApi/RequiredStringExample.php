<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\Rule\MaxLength;
use Params\Rule\MinLength;
use VarMap\VarMap;
use Params\Rule\GetStringOrDefault;
use Params\Rule\GetString;
use Params\Rule\Enum;
use Params\SafeAccess;
use Params\CreateFromVarMap;

class RequiredStringExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'status';

    const MIN_LENGTH = 10;

    const MAX_LENGTH = 100;

    public static function getRules(VarMap $variableMap)
    {
        return [
            self::NAME => [
                new GetString($variableMap),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ],
        ];
    }
}
