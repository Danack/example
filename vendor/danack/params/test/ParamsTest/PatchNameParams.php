<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\CreateFromInput;
use Params\Input;
use Params\Value\PatchEntry;
use Params\Rule\Patch;
use Params\Rule\ExtractPatchPathValue;
use Params\Rule\Trim;
use Params\Rule\MaxLength;
use Params\Rule\MinLength;
use Params\Rule\ValidCharacters;

class PatchNameParams
{
    use CreateFromInput;

    /** @var string */
    private $name;

    /**
     * UpdateWatchListParams constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public static function getRules(Input $input)
    {
        $rules = [
            'name' => [
                new Patch($input, [PatchEntry::REPLACE]),
                new ExtractPatchPathValue(PatchEntry::REPLACE, '/name'),
                new Trim(),
                new MaxLength(128),
                new MinLength(4),
                new ValidCharacters('0-9a-zA-Z_\- ')
            ],
        ];

        return $rules;
    }
}
