<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\CreateFromVarMap;
use Params\CreateOrErrorFromVarMap;
use Params\ParamsValidator;
use Params\Rule\GetStringOrDefault;
use Params\Rule\MaxIntValue;
use Params\Rule\MinIntValue;
use Params\Rule\MaxLength;
use Params\Rule\SkipIfNull;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\Rule\Order;
use Params\Rule\IntegerInput;
use Params\Value\Ordering;
use Params\Params;

class GetArticlesParams
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    const LIMIT_DEFAULT = 10;

    const LIMIT_MIN = 1;
    const LIMIT_MAX = 200;

    const ARTICLE_ID_NAME = 'articleId';
    const ARTICLE_ID_INTERNAL = 'articleId';

    const ARTICLE_DATE_NAME = 'date';
    const ARTICLE_DATE_INTERNAL = 'date';

    const OFFSET_MAX = 1000000000000000;

    /** @return string[] */
    public static function getKnownOrderNames()
    {
        return [
            GetArticlesParams::ARTICLE_ID_NAME,
            GetArticlesParams::ARTICLE_DATE_NAME
        ];
    }

    /** @var Ordering  */
    private $ordering;

    /** @var int  */
    private $limit;

    /** @var int|null  */
    private $afterId;

    public function __construct(Ordering $ordering, int $limit, ?int $afterId)
    {
        $this->ordering = $ordering;
        $this->limit = $limit;
        $this->afterId = $afterId;
    }

    /**
     * @param VarMap $variableMap
     * @throws \Params\Exception\ValidationException
     * @throws \Params\Exception\ParamsException
     */
    public static function getRules(VarMap $variableMap)
    {
        return [
            'order' => [
                new GetStringOrDefault('-date', $variableMap),
                new MaxLength(1024),
                new Order(self::getKnownOrderNames()),
            ],
            'limit' => [
                new GetStringOrDefault((string)self::LIMIT_DEFAULT, $variableMap),
                new IntegerInput(),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX),
            ],
            'after' => [
                new GetStringOrDefault(null, $variableMap),
                new SkipIfNull(),
                new MinIntValue(0),
                new MaxIntValue(self::OFFSET_MAX),
            ],
        ];
    }



    /**
     * @return Ordering
     */
    public function getOrdering(): Ordering
    {
        return $this->ordering;
    }

    /**
     * @return int|null
     */
    public function getAfterId(): ?int
    {
        return $this->afterId;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
