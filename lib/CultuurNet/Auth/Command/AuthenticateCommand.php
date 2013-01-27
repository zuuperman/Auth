<?php

namespace CultuurNet\Auth\Command;

use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

use \Guzzle\Http\Client;
use \Guzzle\Http\Url;

use \Guzzle\Plugin\Cookie\CookiePlugin;
use \Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

use \CultuurNet\Auth\Guzzle\Service;
use \CultuurNet\Auth\Session\JsonSessionFile;

class AuthorizeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('authorize')
            ->setDescription('Perform 3-legged authorization')
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
                'http://example.com'
            );
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $consumer = $this->session->getConsumerCredentials();

        $authService = new Service($this->session->getEndpoint('auth'), $consumer);

        $callback = $in->getOption('callback');

        $temporaryCredentials = $authService->getRequestToken($callback);

        $client = new Client($this->session->getEndpoint('auth'), array('redirect.disable' => true));

        // @todo check if logging in on UiTiD requires cookies?
        $cookiePlugin = new CookiePlugin(new ArrayCookieJar());
        $client->addSubscriber($cookiePlugin);

        $user = $in->getOption('username');
        $password = $in->getOption('password');

        $dialog = $this->getHelperSet()->get('dialog');
        /* @var \Symfony\Component\Console\Helper\DialogHelper $dialog */

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

        $response = $client->post('auth/login', NULL, $postData)->send();

        // @todo check what happens if the app is already authorized

        $postData = array(
            'allow' => 'true',
            'token' => $temporaryCredentials->getToken(),
        );

        $response = $client->post('auth/authorize', NULL, $postData)->send();

        $location = $response->getHeader('Location', true);

        $url = Url::factory($location);

        $oAuthVerifier = $url->getQuery()->get('oauth_verifier');

        $user = $authService->getAccessToken($temporaryCredentials, $oAuthVerifier);
        $this->session->setUser($user);

        $out->writeln('user id: ' . $user->getId());
        $out->writeln('access token: ' . $user->getTokenCredentials()->getToken());
        $out->writeln('access token secret: ' . $user->getTokenCredentials()->getSecret());

        $file = $in->getOption('file');
        if (NULL !== $file) {
            JsonSessionFile::write($this->session, $file);
        }
    }
}
