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
     * @var string
     */
    protected $endpoint;

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
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
