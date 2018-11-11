<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Value\PatchEntries;
use Params\ValidationResult;
use Params\Rule;
use Params\OpenApi\ParamDescription;

class ExtractPatchPathValue implements Rule
{
    /** @var string */
    private $opType;

    /** @var string */
    private $path;

    public function __construct($opType, $path)
    {
        $this->opType = $opType;
        $this->path = $path;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        /** @var PatchEntries $value */
        foreach ($value->getPatchEntries() as $patchEntry) {
            if ($patchEntry->getOp() == $this->opType) {
                if ($patchEntry->getPath() === $this->path) {
                    return ValidationResult::valueResult($patchEntry->getValue());
                }
            }
        }
        $message = sprintf(
            "Patch does not contain an op of type [%s] with path [%s] ",
            $this->opType,
            $this->path
        );

        return ValidationResult::errorResult($message);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO: Implement updateParamDescription() method.
    }
}
