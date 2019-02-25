<?php

declare(strict_types=1);

namespace SlimAuryn\Response;

class TwigResponse
{
    /** @var string */
    private $templateName;
    
    /** @var array */
    private $parameters;

    /** @var int */
    private $status;

    /** @var array */
    private $headers;

    public function __construct(
        string $templateName,
        array $parameters = [],
        int $status = 200,
        array $headers = []
    ) {
        $this->templateName = $templateName;
        $this->parameters = $parameters;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
