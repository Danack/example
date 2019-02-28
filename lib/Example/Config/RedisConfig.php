<?php

declare(strict_types=1);

namespace Example\Config;

class RedisConfig
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $password;

    /**
     *
     * @param string $host
     * @param int $port
     * @param string $password
     */
    public function __construct(string $host, int $port, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
