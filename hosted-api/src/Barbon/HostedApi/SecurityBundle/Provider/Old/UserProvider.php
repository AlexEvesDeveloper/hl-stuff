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
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Barbon\HostedApi\SecurityBundle\Model\User;
use Barbon\IrisRestClient\Client\IrisAgentClient;

/**
 * Responsible for looking up Users from an external service
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var IrisAgentClient
     */
    private $irisAgentClient;

    /**
     * Constructor
     *
     * @param IrisAgentClient $irisAgentClient
     */
    public function __construct(IrisAgentClient $irisAgentClient)
    {
        $this->irisAgentClient = $irisAgentClient;
    }

    /**
     * The $asn and $password parameters technically default to null, but they must always be passed with a value
     * The default values provide backwards compatibility with UserProviderInterface->loadUserByUsername($username)
     *
     * @param string $username
     * @param string $asn
     * @param string $password
     *
     * @throws UsernameNotFoundException
     *
     * @return User
     */
    public function loadUserByUsername($username, $asn = null, $password = null)
    {
        $creds = array(
            'agentSchemeNumber' => $asn, 
            'username' => $username, 
            'password' => $password
        );
        
        $response = $this->irisAgentClient->assume($creds);
        if (false !== $response) {
            $branchUuid = (isset($response['agentBranchId'])) ? $response['agentBranchId'] : null;

            return new User(
                $creds['agentSchemeNumber'], 
                $creds['username'], 
                $creds['password'], 
                array('ROLE_AGENT'),
                $response['consumerKey'],
                $response['consumerSecret'],
                $branchUuid
            );
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     *
     * @throws UnsupportedUserException
     *
     * @return User
     */
    public function refreshUser(UserInterface $user)
    {
        if ( ! $user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername(), $user->getAsn(), $user->getPassword());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Barbon\HostedApi\SecurityBundle\Model\User';
    }
}