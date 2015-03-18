<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * Factory for Guzzle clients suitable for consuming CultuurNet's web services.
 *
 * - OAuth 1.0 enabled.
 * - Uses duplicate query string aggregation in HTTP requests.
 */
class OAuthJavaWebServicesClientFactory implements OAuthClientFactory
{
    /**
     * @inheritdoc
     */
    public function createClient(
        $baseUrl,
        ConsumerCredentials $consumerCredentials,
        TokenCredentials $tokenCredentials = null
    ) {
        if ('/' !== substr($baseUrl, -1, 1)) {
            $baseUrl .= '/';
        }

        $client = new Client($this->getClientConfig($baseUrl));

        $this->attachOAuthSubscriber(
            $client,
            $consumerCredentials,
            $tokenCredentials
        );

        return $client;
    }

    /**
     * @param Client $client
     * @param ConsumerCredentials $consumerCredentials
     * @param TokenCredentials|null $tokenCredentials
     */
    private function attachOAuthSubscriber(
        Client $client,
        ConsumerCredentials $consumerCredentials,
        TokenCredentials $tokenCredentials = null
    ) {
        $oauthSubscriber = new Oauth1(
            $this->getOAuthConfig($consumerCredentials, $tokenCredentials)
        );

        $client->getEmitter()->attach(
            $oauthSubscriber
        );
    }

    /**
     * @param string $baseUrl
     *
     * @return array
     */
    private function getClientConfig($baseUrl) {
        $config = [
            'base_url' => $baseUrl,
            'message_factory' => new DuplicateAggregatorQueryMessageFactory(),
            'defaults' => [
                'auth' => 'oauth',
            ],
        ];

        return $config;
    }

    /**
     * @param ConsumerCredentials $consumerCredentials
     * @param TokenCredentials $tokenCredentials
     * @return array
     */
    private function getOAuthConfig(
        ConsumerCredentials $consumerCredentials,
        TokenCredentials $tokenCredentials = null
    ) {
        $config = [
          'consumer_key' => $consumerCredentials->getKey(),
          'consumer_secret' => $consumerCredentials->getSecret(),
        ];

        if ($tokenCredentials) {
            $config += [
                'token' => $tokenCredentials->getToken(),
                'token_secret' => $tokenCredentials->getSecret()
            ];
        }

        return $config;
    }
}
