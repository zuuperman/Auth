<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Client;

interface OAuthClientFactory
{
    /**
     * @param string $baseUrl
     * @param ConsumerCredentials $credentials
     * @param TokenCredentials $tokenCredentials
     * @return Client
     */
    public function createClient(
      $baseUrl,
      ConsumerCredentials $credentials,
      TokenCredentials $tokenCredentials = null
    );
}
