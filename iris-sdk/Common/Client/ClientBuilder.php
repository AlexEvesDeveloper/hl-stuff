<?php

namespace Barbondev\IRISSDK\Common\Client;

use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Barbondev\IRISSDK\Common\Exception\NamespaceExceptionFactory;
use Barbondev\IRISSDK\Common\Exception\IRISExceptionParser;
use Barbondev\IRISSDK\Common\EventListener\ExceptionEventSubscriber;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Common\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;

/**
 * Class ClientBuilder
 *
 * @package Barbondev\IRISSDK\Common\Client
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ClientBuilder
{
    /**
     * @var LoggerInterface
     */
    private static $logger = null;

    /**
     * @var array
     */
    protected static $commonConfigDefaults = array(

    );

    /**
     * @var array
     */
    protected static $commonConfigRequirements = array(
        ClientOptions::BASE_URL,
        ClientOptions::VERSION,
        ClientOptions::CONSUMER_KEY,
        ClientOptions::CONSUMER_SECRET,
        ClientOptions::SERVICE_DESCRIPTION,
    );

    /**
     * @var string
     */
    protected $clientClass;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $configDefaults = array();

    /**
     * @var array
     */
    protected $configRequirements = array();

    /**
     * Constructor
     *
     * @param string $clientClass
     */
    public function __construct($clientClass)
    {
        $this->clientClass = $clientClass;
    }

    /**
     * Set config
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set client namespace
     *
     * @param string $clientNamespace
     * @return $this
     */
    public function setClientClass($clientNamespace)
    {
        $this->clientClass = $clientNamespace;

        return $this;
    }

    /**
     * Get client namespace
     *
     * @return string
     */
    public function getClientClass()
    {
        return $this->clientClass;
    }

    /**
     * Set config defaults
     *
     * @param array $configDefaults
     * @return $this
     */
    public function setConfigDefaults($configDefaults)
    {
        $this->configDefaults = $configDefaults;

        return $this;
    }

    /**
     * Get config defaults
     *
     * @return array
     */
    public function getConfigDefaults()
    {
        return $this->configDefaults;
    }

    /**
     * Set config requirements
     *
     * @param array $configRequirements
     * @return $this
     */
    public function setConfigRequirements($configRequirements)
    {
        $this->configRequirements = $configRequirements;

        return $this;
    }

    /**
     * Get config requirements
     *
     * @return array
     */
    public function getConfigRequirements()
    {
        return $this->configRequirements;
    }

    /**
     * Build a client based on parameter config, service description,
     * iterator config and exception parser
     *
     * @throws \Guzzle\Common\Exception\InvalidArgumentException
     * @return \Barbondev\IRISSDK\AbstractClient
     */
    public function build()
    {
        $config = Collection::fromConfig(
            $this->config,
            array_merge(self::$commonConfigDefaults, $this->configDefaults),
            array_merge(self::$commonConfigRequirements, $this->configRequirements)
        );

        $serviceDescriptionPath = sprintf(
                $config->get(ClientOptions::SERVICE_DESCRIPTION),
                $config->get(ClientOptions::VERSION)
        );

        $serviceDescription = ServiceDescription::factory($serviceDescriptionPath);

        if ( ! class_exists($this->clientClass)) {
            throw new InvalidArgumentException(
                sprintf('Client class %s does not exist', $this->clientClass));
        }

        /** @var \Barbondev\IRISSDK\AbstractClient $client */
        $client = new $this->clientClass($config);
        $client->setDescription($serviceDescription);

        $exceptionFactory = new NamespaceExceptionFactory(
            new IRISExceptionParser(),
            'Barbondev\IRISSDK\Common\Exception\DefaultException',
            self::$logger
        );

        $client->addSubscriber(
            new ExceptionEventSubscriber($exceptionFactory)
        );

        $oAuthPlugin = new OauthPlugin(array(
            'consumer_key' => $config->get(ClientOptions::CONSUMER_KEY),
            'consumer_secret' => $config->get(ClientOptions::CONSUMER_SECRET),
        ));

        $client->addSubscriber($oAuthPlugin);

        $client->setConfig($config);

        return $client;
    }

    /**
     * Factory off a client builder
     *
     * @param string $clientNamespace
     * @return $this
     */
    public static function factory($clientNamespace)
    {
        return new static($clientNamespace);
    }

    /**
     * Set singleton logger
     *
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger = null)
    {
        self::$logger = $logger;
    }

    /**
     * Get singleton logger
     *
     * @return LoggerInterface
     */
    public static function getLogger()
    {
        return self::$logger;
    }
}
