<?php

namespace CultuurNet\Auth\Command;

use \Symfony\Component\Console\Command\Command as BaseCommand;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

use \CultuurNet\Auth\Session;

use \CultuurNet\Auth\Guzzle\Service;
use \CultuurNet\Auth\ConsumerCredentials;
use \CultuurNet\Auth\Session\JsonSessionFile;

/**
 * Base class for all commands which require CultuurNet OAuth.
 */
abstract class Command extends BaseCommand
{
    /**
     * @var \CultuurNet\Auth\Session
     */
    protected $session;

    /**
     * @return array
     */
    protected function getUserPreferences()
    {
        // get user preferences
        $homeDir = getenv('HOME');
        $confFile = "{$homeDir}/.cultuurnet/auth";

        // @todo Consider using a typed object to hold preferences.
        if (file_exists($confFile)) {
            $preferences = parse_ini_file($confFile);
        }
        else {
            $preferences = array();
        }

        return $preferences;
    }

    /**
     * Constructor.
     *
     * @param string $name The name of the command
     *
     * @throws \LogicException When the command name is empty
     *
     * @api
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this
            ->addOption(
                'consumer-key',
                NULL,
                InputOption::VALUE_REQUIRED,
                'Consumer key'
            )
            ->addOption(
                'consumer-secret',
                NULL,
                InputOption::VALUE_REQUIRED,
                'Consumer secret'
            )
            ->addOption(
                'endpoint',
                NULL,
                InputOption::VALUE_REQUIRED,
                'Base URL of the UiTiD endpoint to authenticate with'
            )
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'File to use for maintaining session (endpoint, consumer key, secret and user access token, secret) accross invocations of the command line client'
            );
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $file = $in->getOption('file');
        if (NULL !== $file) {
            // @todo Catch JsonValidationException and show errors.
            $this->session = JsonSessionFile::read($file);
        }
        else {
            $this->session = new Session();
        }

        $defaultEndpoint = 'http://test.uitid.be/culturefeed/rest';
        $preferences = $this->getUserPreferences();

        $endpoint = $in->getOption('endpoint');
        if ($endpoint) {
            $this->session->setEndpoint($endpoint);
        }
        else if ('' == $this->session->getEndpoint()) {
            $endpoint = isset($preferences['endpoint']) ? $preferences['endpoint'] : $defaultEndpoint;
            $this->session->setEndpoint($endpoint);
        }

        $consumer = $this->session->getConsumerCredentials();
        if (!$consumer) {
            $consumer = new ConsumerCredentials();
            $this->session->setConsumerCredentials($consumer);
        }

        $consumerKey = $in->getOption('consumer-key');
        if ($consumerKey) {
            $consumer->setKey($consumerKey);
        }
        elseif ('' == $consumer->getKey() && isset($preferences['consumer-key'])) {
            $consumer->setKey($preferences['consumer-key']);
        }

        $consumerSecret = $in->getOption('consumer-secret');
        if ($consumerSecret) {
            $consumer->setSecret($consumerSecret);
        }
        else if ('' == $consumer->getSecret() && isset($preferences['consumer-secret'])) {
            $consumer->setSecret($preferences['consumer-secret']);
        }
    }
}
