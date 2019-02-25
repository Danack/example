<?php

declare(strict_types=1);

namespace Params\OpenApi;

use Params\Exception\ParamsException;
use Params\OpenApi\ItemsObject;
use Params\OpenApi\ParamDescription;
use Params\Exception\OpenApiException;

/**
 * Used for testing that Rules that shouldn't affect
 * the parameter descriptions.
 * @codeCoverageIgnore
 */
class ShouldNeverBeCalledParamDescription implements ParamDescription
{
    public function setName(string $name)
    {
        throw new \Exception("setName should not be called.");
    }

    public function setIn(string $in)
    {
        throw new \Exception("setIn should not be called.");
    }

    public function setDescription(string $description)
    {
        throw new \Exception("setDescription should not be called.");
    }

    public function setRequired(bool $required)
    {
        throw new \Exception("setRequired should not be called.");
    }

    public function setSchema(string $schema)
    {
        throw new \Exception("setSchema should not be called.");
    }

    public function setType(string $type)
    {
        throw new \Exception("setType should not be called.");
    }

    public function setFormat(string $format)
    {
        throw new \Exception("setFormat should not be called.");
    }

    public function setAllowEmptyValue(bool $allowEmptyValue)
    {
        throw new \Exception("setAllowEmptyValue should not be called.");
    }

    public function getItems(): ItemsObject
    {
        throw new \Exception("getItems should not be called.");
    }

    public function setItems(ItemsObject $itemsObject)
    {
        throw new \Exception("setItems should not be called.");
    }

    public function setCollectionFormat(string $collectionFormat)
    {
        throw new \Exception("setCollectionFormat should not be called.");
    }

    public function setDefault($default)
    {
        throw new \Exception("setDefault should not be called.");
    }

    public function setMaximum($maximum)
    {
        throw new \Exception("setMaximum should not be called.");
    }

    public function setExclusiveMaximum(bool $exclusiveMaximum)
    {
        throw new \Exception("setExclusiveMaximum should not be called.");
    }

    public function setMinimum($minimum)
    {
        throw new \Exception("setMinimum should not be called.");
    }

    public function setExclusiveMinimum(bool $exclusiveMinimum)
    {
        throw new \Exception("setExclusiveMinimum should not be called.");
    }

    public function setMaxLength(int $maxLength)
    {
        throw new \Exception("setMaxLength should not be called.");
    }

    public function setMinLength(int $minLength)
    {
        throw new \Exception("setMinLength should not be called.");
    }

    public function setPattern(string $pattern)
    {
        throw new \Exception("setPattern should not be called.");
    }

    public function setMaxItems(int $maxItems)
    {
        throw new \Exception("setMaxItems should not be called.");
    }

    public function setMinItems(int $minItems)
    {
        throw new \Exception("setMinItems should not be called.");
    }

    public function setNullAllowed()
    {
        throw new \Exception("setNullAllowed should not be called.");
    }

    public function setUniqueItems(bool $uniqueItems)
    {
        throw new \Exception("setUniqueItems should not be called.");
    }

    public function setEnum(array $enumValues)
    {
        throw new \Exception("setEnum should not be called.");
    }

    public function setMultipleOf($multiple)
    {
        throw new \Exception("setMultipleOf should not be called.");
    }
}
