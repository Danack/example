<?php

declare(strict_types=1);

namespace Params;

class ValidationResult
{
    /** @var string */
    private $value;

    /** @var string|null */
    private $problemMessage;

    /** @var bool */
    private $isFinalResult;

    /**
     * ValidationResult constructor.
     * @param mixed $value
     * @param ?string $problemMessage
     * @param bool $isFinalResult
     */
    private function __construct($value, ?string $problemMessage, bool $isFinalResult)
    {
        $this->value = $value;
        $this->problemMessage = $problemMessage;
        $this->isFinalResult = $isFinalResult;
    }

    /**
     * @param string $message
     * @return ValidationResult
     */
    public static function errorResult(string $message)
    {
        return new self(null, $message, true);
    }

    /**
     * @param mixed $value
     * @return ValidationResult
     */
    public static function valueResult($value)
    {
        return new self($value, null, false);
    }

    /**
     * @param mixed $value
     * @return ValidationResult
     */
    public static function finalValueResult($value)
    {
        return new self($value, null, true);
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getProblemMessage(): ?string
    {
        return $this->problemMessage;
    }

    /**
     * @return bool
     */
    public function isFinalResult(): bool
    {
        return $this->isFinalResult;
    }
}
