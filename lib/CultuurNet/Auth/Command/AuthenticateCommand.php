<?php

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\SubscriberAttachingOAuthClientFactory;
use CultuurNet\Auth\Guzzle\OAuthJavaWebServicesClientFactory;
use CultuurNet\Auth\Guzzle\OAuthJavaWebServicesUserAuthenticatedClientFactory;
use CultuurNet\Auth\Guzzle\Service;
use CultuurNet\Auth\Guzzle\SimpleUserAuthenticatedClientFactory;
use CultuurNet\Auth\ServiceInterface;
use CultuurNet\Auth\Session\JsonSessionFile;
use CultuurNet\Auth\TokenCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use GuzzleHttp\Url;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class AuthenticateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('authenticate')
            ->setDescription('Perform OAuth authentication')
            ->addOption(
                'base-url',
                NULL,
                InputOption::VALUE_REQUIRED,
                'Base URL of the UiTiD service provider to authenticate with'
            )
            ->addOption(
                'username',
                'u',
                InputOption::VALUE_REQUIRED,
                'User name to authenticate with'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_REQUIRED,
                'Password to authenticate with'
            )
            ->addOption(
                'callback',
                NULL,
                InputOption::VALUE_REQUIRED,
                'OAuth callback, for demonstrational purposes',
                'http://example.com'
            );
    }

    /**
     * @param string $baseUrl
     * @param ConsumerCredentials $consumerCredentials
     * @return Service
     */
    private function getAuthService(
        OutputInterface $output,
        $baseUrl,
        ConsumerCredentials $consumerCredentials
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

        $client = $logSubscriberAttachingFactory->createClient(
            $baseUrl,
            $consumerCredentials
        );

        $authService = new Service(
            $client,
            new SimpleUserAuthenticatedClientFactory(
                $logSubscriberAttachingFactory,
                $baseUrl,
                $consumerCredentials
            )
        );

        return $authService;
    }

    /**
     * @param ServiceInterface $authService
     * @param InputInterface $in
     * @return TokenCredentials
     */
    private function getRequestToken($authService, $in) {
        $callback = $in->getOption('callback');
        return $authService->getRequestToken($callback);
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $consumerCredentials = $this->session->getConsumerCredentials();
        $baseUrl = $this->resolveBaseUrl('auth', $in);

        $authService = $this->getAuthService(
            $out,
            $baseUrl,
            $consumerCredentials
        );

        $temporaryCredentials = $this->getRequestToken(
            $authService,
            $in
        );

        $oAuthVerifier = $this->getOAuthVerifier(
            $baseUrl,
            $temporaryCredentials,
            $in,
            $out
        );

        $user = $authService->getAccessToken($temporaryCredentials, $oAuthVerifier);

        $this->session->setUser($user);

        $out->writeln('user id: ' . $user->getId());
        $out->writeln('access token: ' . $user->getTokenCredentials()->getToken());
        $out->writeln('access token secret: ' . $user->getTokenCredentials()->getSecret());

        $sessionFile = $in->getOption('session');
        if (NULL !== $sessionFile) {
            JsonSessionFile::write($this->session, $sessionFile);
        }
    }

    /**
     * Gets the OAauth token verifier by logging in on the website.
     *
     * @param string $baseUrl
     * @param TokenCredentials $temporaryCredentials
     * @param InputInterface $in
     * @param OutputInterface $out
     * @return string the OAuth token verifier.
     */
    private function getOAuthVerifier(
        $baseUrl,
        TokenCredentials $temporaryCredentials,
        InputInterface $in,
        OutputInterface $out
    ) {
        $client = new Client(
            [
                'base_url' => $baseUrl,
                'defaults' => [
                    'allow_redirects' => false,
                    'cookies' => true,
                ],
            ]
        );

        // @todo Register log subscriber as well on this client?

        $user = $in->getOption('username');
        $password = $in->getOption('password');

        /* @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        while (NULL === $user) {
            $user = $dialog->ask($out, 'User name: ');
        }

        while (NULL === $password) {
            $password = $dialog->askHiddenResponse($out, 'Password: ');
        }

        $postData = array(
            'email' => $user,
            'password' => $password,
            'submit' => 'Aanmelden',
            'token' => $temporaryCredentials->getToken(),
        );

        $client->post(
            'auth/login',
            [
                'body' => $postData
            ]
        );

        // @todo check what happens if the app is already authorized

        $postData = array(
            'allow' => 'true',
            'token' => $temporaryCredentials->getToken(),
        );

        $response = $client->post(
            'auth/authorize',
            [
                'body' => $postData
            ]
        );

        $location = $response->getHeader('Location');

        $url = Url::fromString($location);

        $oAuthVerifier = $url->getQuery()->get('oauth_verifier');

        return $oAuthVerifier;
    }
}
