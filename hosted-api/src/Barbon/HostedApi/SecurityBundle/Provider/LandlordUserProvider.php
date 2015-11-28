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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Session\Session;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Barbon\HostedApi\SecurityBundle\Model\LandlordUser;

/**
 * Responsible for looking up and returning Users from an external service
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class LandlordUserProvider extends AbstractUserProvider implements UserProviderInterface
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
    public function loadUserByUsername($username, $password = null)
    {
        // if the password is not passed in, we have arrived here from a login form, so grab it from the request
        if (null === $password) {
            $password = Request::createFromGlobals()->get('_password');
        }

        $credentials = array('email' => $username, 'password' => $password);
        $client = $this->irisEntityManager->getClient();

        // use the MAC in the session to access the cached system credentials
        $authData = $this->session->get('auth-data');

        if ( ! $client->hasValidCredentials($authData->get('systemKey'), $authData->get('systemSecret'))) {
            throw new BadCredentialsException('Invalid System credentials for IRIS');
        }

        // attempt to authenticate and get the Landlords key and secret
        if (false === ($oauthCredentials = $client->assume($credentials))) {
            // invalid credentials
            throw new UsernameNotFoundException('Invalid Landlord credentials for IRIS');            
        }

        // create the User to return it to be stored in the session
        $user = new LandlordUser(
            $authData->get('systemKey'),
            $authData->get('systemSecret'),
            $username,
            $password
        );

        // manually set the consumer key and secret as the username and password do not represent them
        $user->setConsumerKey($oauthCredentials['consumerKey']);
        $user->setConsumerSecret($oauthCredentials['consumerSecret']);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Barbon\HostedApi\SecurityBundle\Model\LandlordUser' === $class;
    }
}
