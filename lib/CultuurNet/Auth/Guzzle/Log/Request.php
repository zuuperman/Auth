<?php

namespace CultuurNet\Auth\Guzzle\Log;

use Symfony\Component\EventDispatcher\Event;

class Request {

  /**
   * The full request object.
   * @var type
   */
  private $request;

  /**
   * Start timestamp of the request in microseconds.
   * @var int
   */
  private $startTime;

  /**
   * Url beïng requested.
   * @var string
   */
  protected $url;

  /**
   * Response object.
   * @var Guzzle\Http\Message\Response
   */
  protected $response;

  /**
   * Total time for the request in microseconds.
   * @var int
   */
  protected $time;

  public function __construct(\Guzzle\Http\Message\RequestInterface $request) {
    $this->request = $request;
    $this->url = $request->getUrl();
    $this->startTime = microtime(TRUE);
  }

  public function getUrl() {
    return $this->url;
  }

  public function setUrl($url) {
    $this->url = $url;
  }

  public function getTime() {
    return $this->time;
  }

  public function setTime($time) {
    $this->time = $time;
  }

  public function getResponse() {
    return $this->response;
  }

  public function setResponse(Guzzle\Http\Message\Response $response) {
    $this->response = $response;
  }

  /**
   * Query has received a result, stop the query.
   */
  public function onRequestSent(Event $event) {
    $stopTime = microtime(TRUE);
    $this->time = round(($stopTime - $this->startTime) * 1000, 2);
    $this->response = $this->request->getResponse();
  }

}
