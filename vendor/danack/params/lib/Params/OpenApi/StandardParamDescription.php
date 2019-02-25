<?php

declare(strict_types=1);

namespace Params\OpenApi;

use Params\Exception\ParamsException;
use Params\Functions;
use Params\OpenApi\ItemsObject;
use Params\OpenApi\ParamDescription;
use Params\Exception\OpenApiException;

class StandardParamDescription implements ParamDescription
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $format;

    private $enumValues = null;

    /** @var null|bool  */
    private $required = null;

    private $minimum = null;

    private $maximum = null;

    /** @var int */
    private $maxLength;

    /** @var int */
    private $minLength;

    private $default = null;

    /** @var bool */
    private $exclusiveMaximum;

    /** @var bool */
    private $exclusiveMinimum;

    /** @var null|bool */
    private $nullAllowed = null;


    public function toArray()
    {
        if ($this->name === null) {
            throw new OpenApiException("Name is null, cannot generate.");
        }

        $array = [];

        $array['name'] = $this->name;

        if ($this->required !== null) {
            $array['required'] = $this->required;
        }

        $schema = $this->generateSchema();

        if (count($schema) !== 0) {
            $array['schema'] = $schema;
        }

        return $array;
    }

    private function generateSchema()
    {
        $schema = [];

        if ($this->minimum !== null) {
            $schema['minimum'] = $this->minimum;
        }
        if ($this->maximum !== null) {
            $schema['maximum'] = $this->maximum;
        }
        if ($this->default !== null) {
            $schema['default'] = $this->default;
        }
        if ($this->type !== null) {
            $schema['type'] = $this->type;
        }
        if ($this->format !== null) {
            $schema['format'] = $this->format;
        }
        if ($this->enumValues !== null) {
            $schema['enum'] = $this->enumValues;
        }
        if ($this->minLength !== null) {
            $schema['minLength'] = $this->minLength;
        }
        if ($this->maxLength !== null) {
            $schema['maxLength'] = $this->maxLength;
        }

        if ($this->exclusiveMaximum !== null) {
            $schema['exclusiveMaximum'] = $this->exclusiveMaximum;
        }
        if ($this->exclusiveMinimum !== null) {
            $schema['exclusiveMinimum'] = $this->exclusiveMinimum;
        }
        if ($this->nullAllowed !== null) {
            $schema['nullable'] = $this->nullAllowed;
        }

        // done
        // maximum
        // minimum
        // maxLength
        // minLength
        // required
        //    format - See Data Type Formats for further details. While relying on JSON Schema's defined formats, the OAS offers a few additional predefined formats.
//default - The default value represents
//    type - Value MUST be a string. Multiple types via an array are not supported.

        //TODO

//title
//multipleOf

//exclusiveMaximum

//exclusiveMinimum

//pattern (This string SHOULD be a valid regular expression, according to the ECMA 262 regular expression dialect)
//maxItems
//minItems
//uniqueItems
//maxProperties
//minProperties




//    allOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    oneOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    anyOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    not - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    items - Value MUST be an object and not an array. Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema. items MUST be present if the type is array.
//properties - Property definitions MUST be a Schema Object and not a standard JSON Schema (inline or referenced).
//    additionalProperties - Value can be boolean or object. Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    description - CommonMark syntax MAY be used for rich text representation.


        return $schema;
    }


    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setIn(string $in)
    {
        // TODO: Implement setIn() method.
        throw new \Exception("setIn not implemented yet.");
    }

    public function setDescription(string $description)
    {
        // TODO: Implement setDescription() method.
        throw new \Exception("setDescription not implemented yet.");
    }

    public function setRequired(bool $required)
    {
        $this->required = $required;
    }

    public function setSchema(string $schema)
    {
        // TODO: Implement setSchema() method.
        throw new \Exception("setSchema not implemented yet.");
    }

    public function setType(string $type)
    {
        $knownTypes = [
           'string',// (this includes dates and files)
           'number',
           'integer',
           'boolean',
           'array',
           'object',
        ];

        if (Functions::array_value_exists($knownTypes, $type) === false) {
            throw new OpenApiException("Type [$type] is not known for the OpenApi spec.");
        }

        $this->type = $type;
    }

    public function setFormat(string $format)
    {
        if ($this->type === 'number') {
            $knownFormats = [
                'float',  // Floating-point numbers.
                'double', // floating-point numbers with double precision.
            ];

            if (Functions::array_value_exists($knownFormats, $format) === false) {
                throw new OpenApiException("Format [$format] is not known for type 'number' the OpenApi spec.");
            }
        }
        else if ($this->type === 'integer') {
            $knownFormats = [
                'int32', // Signed 32-bit integers (commonly used integer type).
                'int64', // Signed 64-bit integers (long type).
            ];

            if (Functions::array_value_exists($knownFormats, $format) === false) {
                throw new OpenApiException("Format [$format] is not known for type 'integer' the OpenApi spec.");
            }
        }


        $this->format = $format;
    }

    public function setAllowEmptyValue(bool $allowEmptyValue)
    {
        // TODO: Implement setAllowEmptyValue() method.
        throw new \Exception("setAllowEmptyValue not implemented yet.");
    }

    public function getItems(): ItemsObject
    {
        // TODO: Implement getItems() method.
        throw new \Exception("getItems not implemented yet.");
    }

    public function setItems(ItemsObject $itemsObject)
    {
        // TODO: Implement setItems() method.
        throw new \Exception("setItems not implemented yet.");
    }

    public function setCollectionFormat(string $collectionFormat)
    {
        // TODO: Implement setCollectionFormat() method.
        throw new \Exception("setCollectionFormat not implemented yet.");
    }

    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }

    public function setExclusiveMaximum(bool $exclusiveMaximum)
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    public function setExclusiveMinimum(bool $exclusiveMinimum)
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    public function setMaxLength(int $maxLength)
    {
        if ($maxLength <= 0) {
            throw new OpenApiException("Max length must be greater than 0");
        }
        $this->maxLength = $maxLength;
    }


    public function setMinLength(int $minLength)
    {
        if ($minLength <= 0) {
            throw new OpenApiException("Min length must be at least 0");
        }
        $this->minLength = $minLength;
    }

    public function setPattern(string $pattern)
    {
        // pattern: '^\d{3}-\d{2}-\d{4}$'
        // TODO: Implement setPattern() method.
        throw new \Exception("setPattern not implemented yet.");
    }

    public function setMaxItems(int $maxItems)
    {
        // TODO: Implement setMaxItems() method.
        throw new \Exception("setMaxItems not implemented yet.");
    }

    public function setMinItems(int $minItems)
    {
        // TODO: Implement setMinItems() method.
        throw new \Exception("setMinItems not implemented yet.");
    }

    public function setNullAllowed()
    {
        $this->nullAllowed = true;
    }

    public function setUniqueItems(bool $uniqueItems)
    {
        // TODO: Implement setUniqueItems() method.
        throw new \Exception("setUniqueItems not implemented yet.");
    }

    public function setEnum(array $enumValues)
    {
        foreach ($enumValues as $enumValue) {
            if (is_string($enumValue) !== true) {
                throw new OpenApiException("All enum values must be strings.");
            }
        }

        $this->enumValues = $enumValues;
    }

    public function setMultipleOf($multiple)
    {
        // TODO: Implement setMultipleOf() method.
        throw new \Exception("setMultipleOf not implemented yet.");
    }
// examples:
//  oneId:
//    summary: Example of a single ID
//    value: [5]   # ?ids=5
//  multipleIds:
//    summary: Example of multiple IDs
//    value: [1, 5, 7]   # ?ids=1,5,7
}
