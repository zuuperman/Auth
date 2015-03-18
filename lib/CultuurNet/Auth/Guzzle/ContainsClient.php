<?php

namespace CultuurNet\Auth\Guzzle;

use \GuzzleHttp\Client;

trait ContainsClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    private function setClient($client)
    {
        $this->client = $client;
    }
}
