<?php

namespace CultuurNet\Auth;

class ConsumerCredentials
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    public function setKey($key)
    {
        // @todo check type
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setSecret($secret)
    {
        // @todo check type
        $this->secret = $secret;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
