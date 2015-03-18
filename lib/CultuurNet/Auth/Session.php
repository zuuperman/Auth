<?php

namespace CultuurNet\Auth;

class Session
{
    /**
     * @var ConsumerCredentials
     */
    protected $consumerCredentials;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $baseUrls;

    /**
     * @return ConsumerCredentials
     */
    public function getConsumerCredentials()
    {
        return $this->consumerCredentials;
    }

    /**
     * @param ConsumerCredentials $consumerCredentials
     * @return void
     */
    public function setConsumerCredentials(ConsumerCredentials $consumerCredentials)
    {
        $this->consumerCredentials = $consumerCredentials;
    }

    /**
     * @param User $user
     * @return void
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User;
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $endpoint
     */
    public function setBaseUrl($api, $url)
    {
        // @todo check type of $key and $endpoint
        $this->baseUrls[$api] = $url;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getBaseUrl($api)
    {
        // @todo check type of $api
        if (isset($this->baseUrls[$api])) {
            return $this->baseUrls[$api];
        } else {
            return null;
        }
    }

    public function getBaseUrls()
    {
        return $this->baseUrls;
    }
}
