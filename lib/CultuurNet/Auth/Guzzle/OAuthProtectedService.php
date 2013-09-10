<?php

namespace CultuurNet\Auth\Guzzle;

use \CultuurNet\Auth\ConsumerCredentials;
use \CultuurNet\Auth\TokenCredentials;

use \Guzzle\Http\Client;
use \Guzzle\Http\Url;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class OAuthProtectedService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Url
     */
    protected $baseUrl;

    /**
     * @var ConsumerCredentials
     */
    protected $consumerCredentials;

    /**
     * @var TokenCredentials
     */
    protected $tokenCredentials;

    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

  /**
     * @param string $baseUrl
     * @param ConsumerCredentials $consumer
     * @param TokenCredentials $tokenCredentials
     */
    public function __construct($baseUrl, ConsumerCredentials $consumerCredentials, TokenCredentials $tokenCredentials = null)
    {
        // @todo check type of $baseUrl
        $this->baseUrl = Url::factory($baseUrl);
        $this->consumerCredentials = $consumerCredentials;
        $this->tokenCredentials = $tokenCredentials;
    }

    /**
     * @return HttpClientFactory
     */
    public function getHttpClientFactory()
    {
        if (!$this->httpClientFactory instanceof HttpClientFactory) {
            $this->httpClientFactory = new DefaultHttpClientFactory();
        }

        return $this->httpClientFactory;
    }

    /**
     * @param HttpClientFactory $httpClientFactory
     *
     * @return $this
     */
    public function setHttpClientFactory(HttpClientFactory $httpClientFactory) {
        $this->httpClientFactory = $httpClientFactory;

        return $this;
    }

    /**
     * @return Client
     */
    protected function getClient() {
        if (!isset($this->client)) {
            $httpClientFactory = $this->getHttpClientFactory();
            $this->client = $httpClientFactory->createClient($this->baseUrl, $this->consumerCredentials, $this->tokenCredentials);
        }

        return $this->client;
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher() {
        if (!isset($this->eventDispatcher)) {
            $this->eventDispatcher = new EventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber) {
        $this->getEventDispatcher()->addSubscriber($subscriber);

        // Also add the subscriber to the Guzzle HTTP client.
        $this->getClient()->addSubscriber($subscriber);
    }

  /**
   * Enable the logging of requests.
   */
  public function enableLogging() {
    $this->getClient()->addSubscriber(\CultuurNet\Auth\Guzzle\Log\RequestLog::getInstance());
  }

  /**
     * @param string $path
     * @return \Guzzle\Http\Url
     */
    protected function getUrlForPath($path) {
        // @todo check type of $path
        $url = clone $this->baseUrl;
        $url->addPath($path);

        return $url;
    }
}
