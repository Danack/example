<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

class ImageJob
{
    /** @var string */
    private $text;

    private function __construct()
    {
    }



    public static function createFromText(string $text)
    {
        $instance = new self();
        $instance->text = $text;

        return $instance;
    }

    public function toString(): string
    {
        $data = [
            'text' => $this->text,
        ];

        return json_encode_safe($data);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        $string = $this->toString();
        $hash = hash('sha256', $string);
        return $hash;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public static function fromString(string $string)
    {
        $data = json_decode_safe($string);
        $instance = new self();
        $instance->text = $data['text'];

        return $instance;
    }

    public function getResultFilename()
    {
        $filename = $this->getId() . ".gif";

        return $filename;
    }
}
