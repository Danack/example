<?php

declare(strict_types=1);

namespace Params;

class ValidationErrors implements \IteratorAggregate
{
    /** @var string[] */
    private $validationProblems;

    /**
     *
     * @param string[] $validationProblems
     */
    public function __construct(array $validationProblems)
    {
        $this->validationProblems = $validationProblems;
    }

    /**
     * @return string[]
     */
    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }

    /**
     * @return object|string[]
     */
    public function getIterator()
    {
        return new \ArrayObject($this->validationProblems);
    }
}
