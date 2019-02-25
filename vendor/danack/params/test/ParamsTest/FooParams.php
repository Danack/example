<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Rule\GetInt;
use Params\Rule\MaxIntValue;
use Params\Rule\MinIntValue;
use Params\SafeAccess;
use VarMap\VarMap;

use Params\Rule\IntegerInput;

class FooParams
{
    use SafeAccess;

    /** @var int  */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public static function getRules(VarMap $variableMap)
    {
        return [
            'limit' => [
                new GetInt($variableMap),
                new IntegerInput(),
                new MinIntValue(0),
                new MaxIntValue(100)
            ]
        ];
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
