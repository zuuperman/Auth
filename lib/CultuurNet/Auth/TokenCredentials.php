<?php

namespace CultuurNet\Auth;

class TokenCredentials
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @param string $token
     * @param string $secret
     */
    public function __construct($token, $secret)
    {
        // @todo verify types
        $this->token = $token;
        $this->secret = $secret;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function getToken()
    {
        return $this->token;
    }
}
