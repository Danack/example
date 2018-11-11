<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;

class GetStringOrDefault implements Rule
{
    private $default;

    /** @var VarMap */
    private $variableMap;

    /**
     * setOrDefaultValidator constructor.
     * @param mixed $default
     */
    public function __construct($default, VarMap $variableMap)
    {
        $this->default = $default;
        $this->variableMap = $variableMap;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(string $name, $value) : ValidationResult
    {
        if ($this->variableMap->has($name) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$this->variableMap->get($name);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
