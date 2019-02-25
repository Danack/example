<?php

declare(strict_types=1);

namespace SlimAuryn\Response;

class ImageResponse implements StubResponse
{
    const TYPE_PNG = 'TYPE_PNG';
    const TYPE_JPG = 'TYPE_JPG';
    const TYPE_GIF = 'TYPE_GIF';

    private $body;

    private $headers = [];

    public function getStatus() : int
    {
        return 200;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * JsonResponse constructor.
     * @param array $headers
     */
    public function __construct($content, $type, array $headers = [])
    {
        $typeHeaders = [
            self::TYPE_PNG => 'image/png',
            self::TYPE_JPG => 'image/jpg',
            self::TYPE_GIF => 'image/gif',
        ];

        $standardHeaders = [
            'Cache-Control' => 'Cache-Control: public, max-age=600',
        ];

        if (array_key_exists($type, $typeHeaders) === true) {
            $standardHeaders['Content-Type'] = $typeHeaders[$type];
        }

        $this->headers = array_merge($standardHeaders, $headers);
        $this->body = $content;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public static function createGuesingTypeFromFilename($content, $imageFilename, array $headers = [])
    {
        $extension = pathinfo($imageFilename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        $extensionTypes = [
            'png'  => self::TYPE_PNG,
            'gif'  => self::TYPE_GIF,
            'jpg'  => self::TYPE_JPG,
            'jpeg' => self::TYPE_JPG
        ];

        $typeGuessed = $extensionTypes[$extension] ?? self::TYPE_JPG;

        return new self($content, $typeGuessed, $headers);
    }
}
