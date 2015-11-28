<?php

namespace Iris\Authentication;

use \Zend_Auth_Storage_Session;

/**
 * Class AbstractAuthorizationToken
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractAuthorizationToken extends Zend_Auth_Storage_Session implements AuthorizationTokenInterface
{
    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $consumerKey;

    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * Constructor
     *
     * @param string|null $consumerKey
     * @param string|null $consumerSecret
     */
    public function __construct($consumerKey = null, $consumerSecret = null)
    {
        parent::__construct(sprintf('%s_iris_token', $this->getName()));

        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;

        if ((null === $consumerKey) || (null === $consumerSecret)) {

            $sessionData = $this->read();

            if (isset($sessionData[$this->getName()])) {

                $sessionData = $sessionData[$this->getName()];

                if ($sessionData instanceof self) {

                    $this->consumerKey = $sessionData->getConsumerKey();
                    $this->consumerSecret = $sessionData->getConsumerSecret();
                    $this->apiBaseUrl = $sessionData->getApiBaseUrl();
                    $this->apiVersion = $sessionData->getApiVersion();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persist()
    {
        $this->write(array(
            $this->getName() => $this,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiBaseUrl($apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextParameters()
    {
        return array(
            'base_url' => $this->apiBaseUrl,
            'version' => $this->apiVersion,
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        );
    }
}