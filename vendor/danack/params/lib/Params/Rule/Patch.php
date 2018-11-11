<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Input;
use Params\ValidationResult;
use Params\Value\AddPatchEntry;
use Params\Value\CopyPatchEntry;
use Params\Value\MovePatchEntry;
use Params\Value\PatchEntry;
use Params\Value\PatchEntries;
use Params\Value\RemovePatchEntry;
use Params\Value\ReplacePatchEntry;
use Params\Value\TestPatchEntry;

class Patch
{
    /** @var Input  */
    private $input;

    /** @var string[] */
    private $allowedOps;

    public function __construct(Input $input, $allowedOps)
    {
        $this->input = $input;
        $this->allowedOps = $allowedOps;
    }

    private function createPatchEntryForObject($op, $path, $patchEntryInput)
    {
        if (in_array($op, $this->allowedOps, true) !== true) {
            $message = sprintf(
                "Op '%s' is not supported for this endpoint.",
                $op
            );
            return [$message, null];
        }

        if ($op === PatchEntry::TEST) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Test operation must contain an entry for 'value'", null];
            }
            return [null, new TestPatchEntry($path, $patchEntryInput->value)];
        }
        else if ($op === PatchEntry::REMOVE) {
            return [null, new RemovePatchEntry($path)];
        }
        else if ($op === PatchEntry::ADD) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Add operation must contain an entry for 'value'", null];
            }
            return [null, new AddPatchEntry($path, $patchEntryInput->value)];
        }
        else if ($op === PatchEntry::REPLACE) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Replace operation must contain an entry for 'value'", null];
            }
            return [null, new ReplacePatchEntry($path, $patchEntryInput->value)];
        }
        else if ($op === PatchEntry::MOVE) {
            if (property_exists($patchEntryInput, 'from') !== true) {
                return ["Move operation must contain an entry for 'from'", null];
            }
            return [null, new MovePatchEntry($path, $patchEntryInput->from)];
        }
        else if ($op === PatchEntry::COPY) {
            if (property_exists($patchEntryInput, 'from') !== true) {
                return ["Copy operation must contain an entry for 'from'", null];
            }
            return [null, new CopyPatchEntry($path, $patchEntryInput->from)];
        }
        else {
            return ["Unknown operation '$op'", null];
        }
    }

    private function createPatchEntryForArray($op, $path, $patchEntryInput)
    {
        if (in_array($op, $this->allowedOps, true) !== true) {
            $message = sprintf(
                "Op '%s' is not supported for this endpoint.",
                $op
            );
            return [$message, null];
        }

        if ($op === PatchEntry::TEST) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Test operation must contain an entry for 'value'", null];
            }
            return [null, new TestPatchEntry($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchEntry::REMOVE) {
            return [null, new RemovePatchEntry($path)];
        }
        else if ($op === PatchEntry::ADD) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Add operation must contain an entry for 'value'", null];
            }
            return [null, new AddPatchEntry($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchEntry::REPLACE) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Replace operation must contain an entry for 'value'", null];
            }
            return [null, new ReplacePatchEntry($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchEntry::MOVE) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Move operation must contain an entry for 'from'", null];
            }
            return [null, new MovePatchEntry($path, $patchEntryInput['from'])];
        }
        else if ($op === PatchEntry::COPY) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Copy operation must contain an entry for 'from'", null];
            }
            return [null, new CopyPatchEntry($path, $patchEntryInput['from'])];
        }
        else {
            return ["Unknown operation '$op'", null];
        }
    }


    private function checkObjectEntryForValidity($patchEntryInput)
    {
        if (property_exists($patchEntryInput, 'op') === false) {
            return ["missing 'op'", null];
        }
        if (property_exists($patchEntryInput, 'path') === false) {
            return ["missing 'path'", null];
        }

        return $this->createPatchEntryForObject(
            $patchEntryInput->op,
            $patchEntryInput->path,
            $patchEntryInput
        );
    }

    private function checkArrayEntryForValidity($patchEntry)
    {
        if (array_key_exists('op', $patchEntry) === false) {
            return ["missing 'op'", null];
        }
        if (array_key_exists('path', $patchEntry) === false) {
            return ["missing 'path'", null];
        }

        return $this->createPatchEntryForArray(
            $patchEntry['op'],
            $patchEntry['path'],
            $patchEntry
        );
    }

    public function __invoke(string $name, $_): ValidationResult
    {
        // TODO - could check $_ is not null here, to prevent Patch being
        // used as anything other than first in list.
        $value = $this->input->get();
        if (is_array($value) !== true) {
            $message = sprintf(
                "Patch '%s' must be an array of values, each with op, path and value set",
                $name
            );

            return ValidationResult::errorResult($message);
        }

        $errorMessages = [];
        $patchEntries = [];

        $count = 0;
        foreach ($value as $patchEntryInput) {
            $error = null;
            $patchEntry = null;

            // todo - do we want to support both array and object decoded patches?
            // for now we will.
            if (is_array($patchEntryInput) === true) {
                [$error, $patchEntry] = $this->checkArrayEntryForValidity($patchEntryInput);
            }
            else if (is_object($patchEntryInput) === true) {
                [$error, $patchEntry] = $this->checkObjectEntryForValidity($patchEntryInput);
            }
            else {
                $error = "Patch entry $count is neither an object or an array.";
            }

            if ($error !== null) {
                $errorMessages[] = "Error for entry $count: " . $error;
            }
            if ($patchEntry !== null) {
                $patchEntries[] = $patchEntry;
            }

            $count++;
        }

        if (count($errorMessages) > 0) {
            $message = sprintf(
                'Data for %s is invalid: %s',
                $name,
                implode(', ', $errorMessages)
            );

            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult(new PatchEntries(...$patchEntries));
    }
}
