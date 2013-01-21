<?php

namespace CultuurNet\Auth;

class User
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var TokenCredentials
     */
    protected $tokenCredentials;

    /**
     * @param string $id
     * @param TokenCredentials $tokenCredentials
     */
    public function __construct($id, TokenCredentials $tokenCredentials)
    {
        $this->id = $id;
        $this->tokenCredentials = $tokenCredentials;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TokenCredentials
     */
    public function getTokenCredentials()
    {
        return $this->tokenCredentials;
    }
}
