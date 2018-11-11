<?php

declare(strict_types = 1);

namespace Params\Exception;

class ValidationException extends \Params\Exception\ParamsException
{
    private $validationProblems;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param array $validationProblems
     * @param \Exception|null $previous
     */
    public function __construct($message, array $validationProblems, \Exception $previous = null)
    {
        $actualMessage = $message . " ";
        $actualMessage .= implode(", ", $validationProblems);

        $this->validationProblems = $validationProblems;

        parent::__construct($actualMessage, $code = 0, $previous);
    }

    /**
     * @return array
     */
    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }

    /**
     * @param string $message
     * @param string[] $validationProblems
     * @throws ValidationException
     */
    public static function throwIfProblems(string $message, array $validationProblems)
    {
        if (count($validationProblems) > 0) {
            throw new ValidationException($message, $validationProblems);
        }
    }
}
