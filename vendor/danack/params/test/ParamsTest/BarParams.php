<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Input;
use Params\SafeAccess;
use Params\Rule\CompleteInput;

class BarParams
{
    use SafeAccess;

    private $values;

    public function __construct($values)
    {
        $this->values = $values;
    }

    public static function getRules(Input $input)
    {
        return [
            'values' => [
                new CompleteInput($input),
            ]
        ];
    }

    public function getValues()
    {
        return $this->values;
    }
}
