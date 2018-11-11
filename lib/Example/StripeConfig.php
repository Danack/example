<?php

namespace Example;

class StripeConfig
{
    private $secret_key;

    private $public_key;

    public function __construct($public_key, $secret_key)
    {
        $this->secret_key = $secret_key;
        $this->public_key = $public_key;
    }

    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }
}
