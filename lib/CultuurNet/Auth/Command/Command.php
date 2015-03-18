<?php

namespace CultuurNet\Auth\Command;

use CultuurNet\Auth\FileUtility;
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
     * @var array
     */
    private $defaults;

    /**
     * @var FileUtility
     */
    private $fileUtility;

    /**
     * @return array
     */
    protected function getDefaults()
    {
        if (!isset($this->defaults)) {
            // get defaults from INI file in current user's home directory
            $homeDir = getenv('HOME');
            $confFile = "{$homeDir}/.cultuurnet/defaults";

            // @todo Consider using a typed object to hold defaults
            if (file_exists($confFile)) {
                $this->defaults = parse_ini_file($confFile);
            }
            else {
                $this->defaults = array();
            }
        }

        return $this->defaults;
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
                'session',
                's',
                InputOption::VALUE_REQUIRED,
                'File to use for maintaining session (base URLs, consumer key, secret and user access token, secret) accross invocations of the command line client'
            );
    }

    protected function getFileUtility() {
        if (!isset($this->fileUtility)) {
            $this->fileUtility = new FileUtility();
        }

        return $this->fileUtility;
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $sessionFile = $in->getOption('session');
        if (NULL !== $sessionFile) {
            // @todo Catch JsonValidationException and show errors.
            $fileUtility = $this->getFileUtility();
            $sessionFile = $fileUtility->expandPath($sessionFile);
            $this->session = JsonSessionFile::read($sessionFile);
        }
        else {
            $this->session = new Session();
        }

        $defaults = $this->getDefaults();

        $consumer = $this->session->getConsumerCredentials();

        if (!$consumer) {
            $consumer = new ConsumerCredentials();
            $this->session->setConsumerCredentials($consumer);
        }

        $consumerKey = $in->getOption('consumer-key');
        if ($consumerKey) {
            $consumer->setKey($consumerKey);
        }
        elseif ('' == $consumer->getKey() && isset($defaults['consumer-key'])) {
            $consumer->setKey($defaults['consumer-key']);
        }

        $consumerSecret = $in->getOption('consumer-secret');
        if ($consumerSecret) {
            $consumer->setSecret($consumerSecret);
        }
        else if ('' == $consumer->getSecret() && isset($defaults['consumer-secret'])) {
            $consumer->setSecret($defaults['consumer-secret']);
        }
    }

    /**
     *
     */
    protected function resolveBaseUrl($api, InputInterface $in = NULL, $default = 'http://acc.uitid.be/uitid/rest')
    {
        if (NULL === $this->session) {
            // @todo throw exception as session isn't initialized yet
        }

        if ($in->hasOption($api . '-base-url')) {
            $baseUrl = $in->getOption($api . '-base-url');
        }
        else if ($in->hasOption('base-url')) {
            $baseUrl = $in->getOption('base-url');
        }

        if (isset($baseUrl)) {
            $this->session->setBaseUrl($api, $baseUrl);
        }
        else if ('' == $this->session->getBaseUrl($api)) {
            $baseUrl = isset($this->defaults['base-url'][$api]) ? $this->defaults['base-url'][$api] : $default;
            $this->session->setBaseUrl($api, $baseUrl);
        }
        else {
            $baseUrl = $this->session->getBaseUrl($api);
        }

        if (substr($baseUrl, -1, 1) !== '/') {
            $baseUrl .= '/';
        }

        return $baseUrl;
    }
}
