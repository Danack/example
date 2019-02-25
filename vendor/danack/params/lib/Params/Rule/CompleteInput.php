<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Input;
use Params\OpenApi\ParamDescription;
use Params\Rule;
use Params\ValidationResult;
use Params\Value\AddPatchEntry;
use Params\Value\CopyPatchEntry;
use Params\Value\MovePatchEntry;
use Params\Value\PatchEntry;
use Params\Value\PatchEntries;
use Params\Value\RemovePatchEntry;
use Params\Value\ReplacePatchEntry;
use Params\Value\TestPatchEntry;

/**
 * Used for testing
 */
class CompleteInput implements Rule
{
    /** @var Input  */
    private $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    public function __invoke(string $name, $_): ValidationResult
    {
        return ValidationResult::valueResult($this->input->get());
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO: Implement updateParamDescription() method.
    }
}
