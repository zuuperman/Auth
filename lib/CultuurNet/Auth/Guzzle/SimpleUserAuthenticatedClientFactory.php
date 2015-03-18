<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;

class SimpleUserAuthenticatedClientFactory implements UserAuthenticatedClientFactory
{
    /**
     * @var OAuthClientFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var ConsumerCredentials
     */
    protected $consumerCredentials;

    /**
     * @param OAuthClientFactory $factory
     * @param string $baseUrl
     * @param ConsumerCredentials $consumerCredentials
     */
    public function __construct(
        OAuthClientFactory $factory,
        $baseUrl,
        ConsumerCredentials $consumerCredentials
    ) {
        $this->baseUrl = $baseUrl;
        $this->consumerCredentials = $consumerCredentials;
        $this->factory = $factory;
    }

    public function createClient(TokenCredentials $tokenCredentials)
    {
        return $this->factory->createClient(
            $this->baseUrl,
            $this->consumerCredentials,
            $tokenCredentials
        );
    }

}
