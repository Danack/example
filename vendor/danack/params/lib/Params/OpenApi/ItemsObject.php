<?php

declare(strict_types=1);

namespace Params\OpenApi;

interface ItemsObject
{
    // string	Required. The internal type of the array. The value MUST
    // be one of "string", "number", "integer", "boolean", or "array".
    // Files and models are not allowed.
    public function setType(string $type);

    // string	The extending format for the previously mentioned type. See Data Type Formats for further details.
    public function setFormat(string $format);

    //     Items Object	Required if type is "array". Describes the type of items in the array.
    //     collectionFormat	string	Determines the format of the array if type array is used. Possible values are:
    //     csv - comma separated values foo,bar.
    //     ssv - space separated values foo bar.
    //     tsv - tab separated values foo\tbar.
    //     pipes - pipe separated values foo|bar.
    //     Default value is csv.
    //     default	*	Declares the value of the item that the server will use if none is provided.
    //  (Note: "default" has no meaning for required items.)
    // See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-6.2.
    // Unlike JSON Schema this value MUST conform to the defined type for the data type.
    public function setItems(string $items);

    // number	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2.
    public function setMaximum($maximum);

    // boolean	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2.
    public function setExclusiveMaximum(bool $exclusiveMinimum);

    // number	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3.
    public static function setMinimum($number);

    // boolean	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3.
    public function setExclusiveMinimum(bool $exclusiveMinimum);

    // integer	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.1.
    public function setMaxLength(int $maxLength);

    // integer	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.2.
    public function setMinLength(int $minLength);

    // string	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.3.
    public function setPattern(string $pattern);

    // integer	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.2.
    public function setMaxItems(int $maxItems);

    // integer	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.3.
    public function setMinItems(int $minItems);

    // boolean	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.4.
    public function setUniqueItems(bool $uniqueItems);

    // [*]	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.5.1.
    // The value of this keyword MUST be an array.  This array MUST have at
    // least one element.  Elements in the array MUST be unique.
    // Elements in the array MAY be of any type, including null.
    public function setEnum(array $enum);

    // number	See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.1.
    public function setMultipleOf($number);
}
