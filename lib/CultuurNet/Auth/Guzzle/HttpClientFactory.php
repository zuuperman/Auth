<?php

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;

use Guzzle\Http\Client;

interface HttpClientFactory
{
  /**
   * @param string $baseUrl
   * @param ConsumerCredentials $consumer
   * @param TokenCredentials $tokenCredentials
   *
   * @return Client
   */
  public function createClient($baseUrl, ConsumerCredentials $consumer, TokenCredentials $tokenCredentials);
}
