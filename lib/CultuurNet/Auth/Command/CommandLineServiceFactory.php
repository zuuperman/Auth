<?php

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\OAuthClientFactory;
use CultuurNet\Auth\Guzzle\OAuthJavaWebServicesClientFactory;
use CultuurNet\Auth\Guzzle\SubscriberAttachingOAuthClientFactory;
use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandLineServiceFactory
{

    /**
     * Creates a service suitable for the command-line.
     *
     * @param InputInterface $in
     * @param OutputInterface $out
     * @param string $baseUrl
     * @param ConsumerCredentials $consumer
     * @param TokenCredentials $token
     *
     * @return mixed
     */
    abstract public function createService(
        InputInterface $in,
        OutputInterface $out,
        $baseUrl,
        ConsumerCredentials $consumer,
        TokenCredentials $token = null
    );

    /**
     * @param OutputInterface $output
     * @return OAuthClientFactory
     */
    protected function getOAuthClientFactory(
        OutputInterface $output
    ) {
        $baseFactory = new OAuthJavaWebServicesClientFactory();
        $logSubscriberAttachingFactory = new SubscriberAttachingOAuthClientFactory(
            $baseFactory,
            [
                new LogSubscriber(
                    new ConsoleLogger(
                        $output
                    ),
                    Formatter::DEBUG
                ),
            ]
        );

        return $logSubscriberAttachingFactory;
    }
}
