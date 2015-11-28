<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\Provider;


use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Barbon\HostedApi\SecurityBundle\Model\AgentUser;

/**
 * Responsible for looking up and returning Users from an external service
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class AgentUserProvider extends AbstractUserProvider implements UserProviderInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param Session $session
     */
    public function __construct(IrisEntityManager $irisEntityManager, Session $session)
    {
        parent::__construct($irisEntityManager);
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($credentials)
    {
        $client = $this->irisEntityManager->getClient();

        // validate the system credentials
        if ( ! $client->hasValidCredentials($credentials['systemKey'], $credentials['systemSecret'])) {
            throw new BadCredentialsException('Invalid System credentials for IRIS');
        }

        // validate the agent credentials
        if ( ! $client->hasValidCredentials($credentials['consumerKey'], $credentials['consumerSecret'])) {
            throw new BadCredentialsException('Invalid Agent credentials for IRIS');
        }

        // remove current oauth listener and register new one
        $this->irisEntityManager->getClient()->resume($credentials);

        //$response = $this->irisClient->get('/referencing/v1/agent/branch/uniqueIndetifier');

        $user = new AgentUser(
            $credentials['systemKey'],
            $credentials['systemSecret'],
            $credentials['consumerKey'],
            $credentials['consumerSecret']
        );

        // Store auth data in session for brand endpoints called outside agent firewall
        $authData = new ParameterBag(array(
            'systemKey' => $credentials['systemKey'],
            'systemSecret' => $credentials['systemSecret'],
            'vendorKey' => $credentials['systemKey'],
            'vendorSecret' => $credentials['systemSecret'],
            'consumerKey' => $credentials['consumerKey'],
            'consumerSecret' => $credentials['consumerSecret']
        ));

        $this->session->set('auth-data', $authData);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Barbon\HostedApi\SecurityBundle\Model\AgentUser' === $class;
    }
}
