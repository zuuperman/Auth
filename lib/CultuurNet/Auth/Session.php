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
    protected $endpoints;

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
    public function setEndpoint($key, $endpoint)
    {
        // @todo check type of $key and $endpoint
        $this->endpoints[$key] = $endpoint;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getEndpoint($key)
    {
        // @todo check type of $key
        if (isset($this->endpoints[$key])) {
            return $this->endpoints[$key];
        }
        else {
            return NULL;
        }
    }

    public function getEndpoints() {
        return $this->endpoints;
    }
}
