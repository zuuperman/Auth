<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\Service as AuthService;
use CultuurNet\Auth\Guzzle\SimpleUserAuthenticatedClientFactory;
use CultuurNet\Auth\TokenCredentials;
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
      ConsumerCredentials $consumerCredentials,
      TokenCredentials $tokenCredentials = null
    ) {
        $factory = $this->getOAuthClientFactory($out);

        $client = $factory->createClient(
            $baseUrl,
            $consumerCredentials,
            $tokenCredentials
        );

        $service = new AuthService(
            $client,
            new SimpleUserAuthenticatedClientFactory(
                $factory,
                $baseUrl,
                $consumerCredentials
            )
        );

        return $service;
    }
}
