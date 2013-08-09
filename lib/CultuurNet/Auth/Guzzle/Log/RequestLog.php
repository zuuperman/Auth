<?php

namespace CultuurNet\Auth\Guzzle\Log;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class RequestLog implements EventSubscriberInterface {

  protected static $instance;

  /**
   * @var array of requests done to api.
   */
  protected $requests = array();

  /**
   * Factory method to get the query log.
   */
  public static function getInstance() {

    if (!isset(self::$instance)) {
      self::$instance = new RequestLog();
    }

    return self::$instance;

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array(
      'curl.callback.write' => array('onCurlWrite', 255),
      'curl.callback.read' => array('onCurlRead', 255),
      'request.before_send' => array('onRequestBeforeSend', 255),
      'request.sent' => array('onRequestSent', 255)
    );
  }

  /**
   * Called before a request is sent
   *
   * @param Event $event
   */
  public function onRequestBeforeSend(Event $event) {

    $request = $event['request'];
    $request->getParams()->set('request_log', new Request($request));
  }

  /**
   * Triggers the actual log write when a request completes
   *
   * @param Event $event
   */
  public function onRequestSent(Event $event) {

    $request = $event['request'];

    if ($request_log = $request->getParams()->get('request_log')) {
      $request_log->onRequestSent($event);
      $this->requests[] = $request_log;
    }
  }

  /**
   * @return array
   */
  public function getRequests() {
    return $this->requests;
  }

  public function getRequestCount() {
    return count($this->requests);
  }

}
