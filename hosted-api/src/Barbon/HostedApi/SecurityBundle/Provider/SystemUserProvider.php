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


use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Barbon\HostedApi\SecurityBundle\Model\SystemUser;

/**
 * Responsible for looking up and returning Users from an external service
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class SystemUserProvider extends AbstractUserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     */    
    public function loadUserByUsername($credentials)
    {
        $client = $this->irisEntityManager->getClient();

        if ( ! $client->hasValidCredentials($credentials['systemKey'], $credentials['systemSecret'])) {
            throw new BadCredentialsException('Invalid System credentials for IRIS');
        }

        $user = new SystemUser(
            $credentials['systemKey'],
            $credentials['systemSecret'],
            $credentials['systemKey'],
            $credentials['systemSecret']
        );

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Barbon\HostedApi\SecurityBundle\Model\SystemUser' === $class;
    }
}
