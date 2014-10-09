<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\Service as AuthService;
use CultuurNet\Auth\TokenCredentials;
use Guzzle\Log\ClosureLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AuthServiceFactory extends CommandLineServiceFactory
{
    
    /**
     * @inheritdoc
     *
     * @return AuthService
     */
    public function createService(
      InputInterface $in,
      OutputInterface $out,
      $baseUrl,
      ConsumerCredentials $consumer,
      TokenCredentials $token = null)
    {
        $service = new AuthService($baseUrl, $consumer);

        $this->registerSubscribers($in, $out, $service);

        return $service;
    }
}
