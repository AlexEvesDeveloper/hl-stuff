<?php

namespace Iris\Authentication;

use Barbondev\IRISSDK\Common\Exception\AuthenticationException;
use Zend_Log;

/**
 * Class Authentication
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Authentication
{
    /**
     * @var AgentAuthenticator
     */
    private $agentAuthenticator;

    /**
     * @var LandlordAuthenticator
     */
    private $landlordAuthenticator;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var Zend_Log
     */
    private $logger;

    /**
     * Constructor
     *
     * @param AgentAuthenticator $agentAuthenticator
     * @param LandlordAuthenticator $landlordAuthenticator
     * @param string $apiBaseUrl
     * @param string $apiVersion
     * @param Zend_log $logger
     */
    public function __construct(
        AgentAuthenticator $agentAuthenticator,
        LandlordAuthenticator $landlordAuthenticator,
        $apiBaseUrl,
        $apiVersion,
        Zend_Log $logger
    ) {
        $this->agentAuthenticator = $agentAuthenticator;
        $this->landlordAuthenticator = $landlordAuthenticator;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->apiVersion = $apiVersion;
        $this->logger = $logger;
    }

    /**
     * Authenticate agent, return token on success and FALSE on fail. Also
     * stores authorization token in auth session
     *
     * @param string $agentSchemeNumber
     * @param string $username
     * @param string $password
     * @return bool|AgentAuthorizationToken
     */
    public function authenticateAgent($agentSchemeNumber, $username, $password)
    {
        try {
            $token = $this
                ->agentAuthenticator
                ->authenticate($agentSchemeNumber, $username, $password)
            ;
        }
        catch (AuthenticationException $e) {
            $this->logger->log(sprintf('Authentication of agent failed: %s', $e->getMessage()), Zend_Log::DEBUG);
            return false;
        }

        if ( ! ($token instanceof AuthorizationTokenInterface)) {
            return false;
        }

        $token
            ->setApiBaseUrl($this->apiBaseUrl)
            ->setApiVersion($this->apiVersion)
        ;

        $token->persist();

        return $token;
    }

    /**
     * Authenticate landlord, return TRU on success and FALSE on fail. Also
     * stores authorization token in auth session
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authenticateLandlord($email, $password)
    {
        try {
            $token = $this
                ->landlordAuthenticator
                ->authenticate($email, $password)
            ;
        }
        catch (AuthenticationException $e) {
            $this->logger->log(sprintf('Authentication of landlord failed: %s', $e->getMessage()), Zend_Log::DEBUG);
            return false;
        }

        if ( ! ($token instanceof AuthorizationTokenInterface)) {
            return false;
        }

        $token
            ->setApiBaseUrl($this->apiBaseUrl)
            ->setApiVersion($this->apiVersion)
        ;

        $token->persist();

        return true;
    }

    /**
     * Set agentAuthenticator
     *
     * @param \Iris\Authentication\AgentAuthenticator $agentAuthenticator
     * @return $this
     */
    public function setAgentAuthenticator(AgentAuthenticator $agentAuthenticator)
    {
        $this->agentAuthenticator = $agentAuthenticator;
        return $this;
    }

    /**
     * Get agentAuthenticator
     *
     * @return \Iris\Authentication\AgentAuthenticator
     */
    public function getAgentAuthenticator()
    {
        return $this->agentAuthenticator;
    }

    /**
     * Set landlordAuthenticator
     *
     * @param \Iris\Authentication\LandlordAuthenticator $landlordAuthenticator
     * @return $this
     */
    public function setLandlordAuthenticator(LandlordAuthenticator $landlordAuthenticator)
    {
        $this->landlordAuthenticator = $landlordAuthenticator;
        return $this;
    }

    /**
     * Get landlordAuthenticator
     *
     * @return \Iris\Authentication\LandlordAuthenticator
     */
    public function getLandlordAuthenticator()
    {
        return $this->landlordAuthenticator;
    }
}