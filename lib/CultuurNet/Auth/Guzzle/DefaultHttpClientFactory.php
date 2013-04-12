<?php

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DefaultHttpClientFactory implements HttpClientFactory
{
    /**
     * @var array list of subscribers
     */
    protected $subscribers = array();

    /**
     * @param string $baseUrl
     * @param ConsumerCredentials $consumerCredentials
     * @param TokenCredentials $tokenCredentials
     *
     * @return Client
     */
    public function createClient($baseUrl, ConsumerCredentials $consumerCredentials, TokenCredentials $tokenCredentials = null)
    {
        $oAuthConfig = array(
            'consumer_key' => $consumerCredentials->getKey(),
            'consumer_secret' => $consumerCredentials->getSecret(),
        );

        if ($tokenCredentials instanceof TokenCredentials) {
            $oAuthConfig += array(
                'token' => $tokenCredentials->getToken(),
                'token_secret' => $tokenCredentials->getSecret(),
            );
        }

        $oAuth = new OauthPlugin($oAuthConfig);

        $client = new Client();

        $client
            ->setBaseUrl($baseUrl)
            ->addSubscriber($oAuth);

        foreach ($this->subscribers as $subscriber) {
            $client->addSubscriber($subscriber);
        }

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber) {
        $this->subscribers[] = $subscriber;

        return $this;
    }
}
