<?php

namespace CultuurNet\Auth\Command;


use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\OAuthProtectedService;
use CultuurNet\Auth\TokenCredentials;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Guzzle\Log\ClosureLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;

abstract class CommandLineServiceFactory {

    /**
     * Creates an auth service suitable for the command-line.
     *
     * @param InputInterface $in
     * @param OutputInterface $out
     * @param string $baseUrl
     * @param ConsumerCredentials $consumer
     * @param TokenCredentials $token
     *
     * @return OAuthProtectedService
     */
    public abstract function createService(
      InputInterface $in,
      OutputInterface $out,
      $baseUrl,
      ConsumerCredentials $consumer,
      TokenCredentials $token = null);

    public function registerSubscribers(InputInterface $in, OutputInterface $out, OAuthProtectedService $service)
    {
        if (TRUE == $in->getOption('debug')) {
            $adapter = new ClosureLogAdapter(function ($message, $priority, $extras) use ($out) {
                // @todo handle $priority
                $out->writeln($message);
            });
            $format = "\n\n# Request:\n{request}\n\n# Response:\n{response}\n\n# Errors: {curl_code} {curl_error}\n\n";
            $log = new LogPlugin($adapter, $format);

            $service->getHttpClientFactory()->addSubscriber($log);
        }
    }

} 