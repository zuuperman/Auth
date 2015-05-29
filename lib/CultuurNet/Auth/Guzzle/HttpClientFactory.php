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
   * @param string $oauthCallback
   *
   * @return Client
   */
  public function createClient(
      $baseUrl,
      ConsumerCredentials $consumer,
      TokenCredentials $tokenCredentials,
      $oauthCallback = null
  );


    /**
     * @param EventSubscriberInterface $subscriber
     *
     * @return $this
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
