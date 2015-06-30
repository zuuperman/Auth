<?php

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;

use Guzzle\Http\Client;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface HttpClientFactory
{
  /**
   * @param string $baseUrl
   * @param ConsumerCredentials $consumer
   * @param TokenCredentials $tokenCredentials
   * @param array $additionalOAuthParameters
   *
   * @return Client
   */
  public function createClient(
      $baseUrl,
      ConsumerCredentials $consumer,
      TokenCredentials $tokenCredentials = null,
      array $additionalOAuthParameters = array()
  );


    /**
     * @param EventSubscriberInterface $subscriber
     *
     * @return $this
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
