<?php

declare(strict_types=1);

namespace Example\Queue;

class PrintUrlToPdfJob
{
    /** @var string */
    private $url;

    /** @var string */
    private $filename;

    public function __construct(string $url, string $filename)
    {
        $this->url = $url;
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }


    public function toArray()
    {
        return [
            'url' => $this->url,
            'filename' => $this->filename,
        ];
    }

    public static function fromData($data)
    {
        $data = json_decode_safe($data);

        return new self($data['url'], $data['filename']);
    }
}
