<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

class ValidDatetime implements Rule
{
    const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date time apparently';

    public function __invoke(string $name, $value): ValidationResult
    {
        $dateTime = \DateTime::createFromFormat(\DateTime::RFC3339, $value);
        if ($dateTime instanceof \DateTime) {
            return ValidationResult::valueResult($dateTime);
        }

//        $dateTime = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $value);
//        if ($dateTime instanceof \DateTime) {
//            return ValidationResult::valueResult($dateTime);
//        }

// todo - is there any value in returning these errors?
//            if (count($lastErrors['warnings']) !== 0 || count($lastErrors['errors']) !== 0) {
//                //$errorsArray = array_merge($lastErrors['warnings'], $lastErrors['errors']);
//                return ValidationResult::errorResult(
//                    self::ERROR_INVALID_DATETIME // . implode(". ", $errorsArray)
//                );
//            }

        return ValidationResult::errorResult(self::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
