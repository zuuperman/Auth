<?php
/**
 * @file
 */

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\Service;
use Guzzle\Log\ClosureLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AuthServiceFactory
{
    private function __construct() {}
    
    /**
     * Creates an auth service suitable for the command line.
     *
     * @param InputInterface $in
     * @param string $baseUrl
     * @param ConsumerCredentials $consumer
     *
     * @return Service
     */
    public static function createAuthService(
      InputInterface $in,
      OutputInterface $out,
      $baseUrl,
      ConsumerCredentials $consumer)
    {
        $authService = new Service($baseUrl, $consumer);

        if (TRUE == $in->getOption('debug')) {
            $adapter = new ClosureLogAdapter(function ($message, $priority, $extras) use ($out) {
                  // @todo handle $priority
                  $out->writeln($message);
              });
            $format = "\n\n# Request:\n{request}\n\n# Response:\n{response}\n\n# Errors: {curl_code} {curl_error}\n\n";
            $log = new LogPlugin($adapter, $format);

            $authService->getHttpClientFactory()->addSubscriber($log);
        }

        return $authService;
    }
}
