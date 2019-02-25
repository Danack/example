<?php

declare(strict_types=1);

namespace Example\Params;

use Params\CreateOrErrorFromVarMap;
use Params\Rule\GetIntOrDefault;
use Params\Rule\MaxIntValue;
use Params\Rule\MinIntValue;
use Params\Rule\MaxLength;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\Rule\Enum;
use Params\Rule\GetString;

class WordSearchParams
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    const MATCH_TYPE_EXACT = 'exact';
    const MATCH_TYPE_BEGINS_WITH = 'begins_with';
    const MATCH_TYPE_ENDS_WITH = 'end_with';

    const LIMIT_DEFAULT = 50;

    const MATCH_TYPES = [
        self::MATCH_TYPE_EXACT,
        self::MATCH_TYPE_BEGINS_WITH,
        self::MATCH_TYPE_ENDS_WITH
    ];

    /** @var string */
    private $search_string;

    /** @var string */
    private $match_type;

    /** @var int */
    private $limit;

    public function __construct(
        string $search_string,
        string $match_type,
        int $limit
    ) {
        $this->search_string = $search_string;
        if (in_array($match_type, self::MATCH_TYPES, true) !== true) {
            throw new \Exception("Unknown match type [$match_type]");
        }
        $this->match_type = $match_type;
        $this->limit = $limit;
    }

    /**
     * @param VarMap $variableMap
     * @return array
     */
    public static function getRules(VarMap $variableMap)
    {
        return [
            'search_string' => [
                new GetString($variableMap),
                new MaxLength(32)
            ],
            'match_type' => [
                new GetString($variableMap),
                new Enum(self::MATCH_TYPES),
            ],
            'limit' => [
                new GetIntOrDefault(self::LIMIT_DEFAULT, $variableMap),
                new MinIntValue(1),
                new MaxIntValue(500),
            ],
        ];
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->search_string;
    }

    /**
     * @return string
     */
    public function getMatchType(): string
    {
        return $this->match_type;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
