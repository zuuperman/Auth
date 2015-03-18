<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Guzzle;

use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Client;

interface UserAuthenticatedClientFactory
{
    /**
     * @param TokenCredentials $tokenCredentials
     * @return Client
     */
    public function createClient(TokenCredentials $tokenCredentials);
}
