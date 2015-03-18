<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Event\SubscriberInterface;

class SubscriberAttachingOAuthClientFactory implements OAuthClientFactory
{
    /**
     * @var OAuthClientFactory
     */
    protected $factory;

    /**
     * @var SubscriberInterface[]
     */
    protected $subscribers;

    /**
     * @param OAuthClientFactory $factory
     * @param SubscriberInterface[] $subscribers
     */
    public function __construct(
        OAuthClientFactory $factory,
        array $subscribers
    ) {
        $this->factory = $factory;
        $this->subscribers = $subscribers;
    }

    /**
     * @inheritdoc
     */
    public function createClient(
        $baseUrl,
        ConsumerCredentials $credentials,
        TokenCredentials $tokenCredentials = null
    ) {
        $client = $this->factory->createClient(
            $baseUrl,
            $credentials,
            $tokenCredentials
        );

        foreach ($this->subscribers as $subscriber) {
            $client->getEmitter()->attach($subscriber);
        }

        return $client;
    }
}
