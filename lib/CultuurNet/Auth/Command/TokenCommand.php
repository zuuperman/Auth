<?php

namespace CultuurNet\Auth\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TokenCommand extends Command
{
    public function configure()
    {
        $this
          ->setName('token')
          ->setDescription('Get a temporary token that can be used for authentication')
          ->addOption(
            'base-url',
            NULL,
            InputOption::VALUE_REQUIRED,
            'Base URL of the UiTiD service provider to authenticate with'
          )
          ->addOption(
            'debug',
            NULL,
            InputOption::VALUE_NONE,
            'Output full HTTP traffic for debugging purposes'
          );
    }

    public function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $consumer = $this->session->getConsumerCredentials();

        $authBaseUrl = $this->resolveBaseUrl('auth', $in);

        $authService = AuthServiceFactory::createAuthService($in, $out, $authBaseUrl, $consumer);

        $temporaryCredentials = $authService->getRequestToken();

        $out->writeln($temporaryCredentials->getToken());
    }

}
