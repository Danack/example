<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\Rule\GetInt;
use Params\Rule\MaxIntValue;
use Params\Rule\MaxLength;
use Params\Rule\MinIntValue;
use Params\Rule\MinLength;
use VarMap\VarMap;
use Params\Rule\GetStringOrDefault;
use Params\Rule\GetString;
use Params\Rule\Enum;
use Params\SafeAccess;
use Params\CreateFromVarMap;

class RequiredIntExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'amount';

    const MIN = 10;

    const MAX = 100;

    public static function getRules(VarMap $variableMap)
    {
        return [
            self::NAME => [
                new GetInt($variableMap),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ],
        ];
    }
}
