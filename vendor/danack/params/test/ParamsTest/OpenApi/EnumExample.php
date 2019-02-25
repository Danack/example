<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use VarMap\VarMap;
use Params\Rule\GetStringOrDefault;
use Params\Rule\GetString;
use Params\Rule\Enum;
use Params\SafeAccess;
use Params\CreateFromVarMap;

//class EnumExample
//{
//
//    use SafeAccess;
//    use CreateFromVarMap;
//
//    const NAME = 'status';
//
//    const VALUES = [
//        'available',
//        'pending',
//        'sold'
//    ];
//
//    public static function getRules(VarMap $variableMap)
//    {
//        return [
//            self::NAME => [
//                new GetString($variableMap),
//                new Enum(self::VALUES),
//            ],
//        ];
//    }
//}
