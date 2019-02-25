<?php

declare(strict_types=1);

namespace Example\Config;

class TwigConfig
{
    /** @var bool */
    private $cache;

    /** @var bool */
    private $debug;

    /**
     *
     * @param bool $cache
     * @param bool $debug
     */
    public function __construct(bool $cache, bool $debug)
    {
        $this->cache = $cache;
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isCache(): bool
    {
        return $this->cache;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }
}
