<?php

declare(strict_types=1);

namespace Example;

class MandrillConfig
{
    /** @var string */
    private $api_key;

    /** @var string */
    private $template;

    /** @var string */
    private $subject;

    /** @var string */
    private $from_email;

    /** @var string */
    private $from_name;

    /**
     * MandrillConfig constructor.
     * @param string $api_key
     * @param string $template
     * @param string $subject
     * @param string $from_email
     * @param string $from_name
     */
    public function __construct(string $api_key, string $template, string $subject, string $from_email, string $from_name)
    {
        $this->api_key = $api_key;
        $this->template = $template;
        $this->subject = $subject;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->from_email;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->from_name;
    }
}
