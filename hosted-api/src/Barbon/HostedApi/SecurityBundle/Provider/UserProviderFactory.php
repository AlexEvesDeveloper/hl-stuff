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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Responsible for looking up and returning Users from an external service
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class UserProviderFactory
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Container
     */
    protected $container;
    
    const SYSTEM_USER = 'system';
    const AGENT_USER = 'agent';
    const LANDLORD_USER = 'landlord';
    const USER_TYPES = 'system|agent|landlord';

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     * @param Container $container
     */
    public function __construct(RequestStack $requestStack, Container $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * Get a UserProviderInterface object based on the HTTP Request
     *
     * @return UserProviderInterface
     */
    public function getUserProvider()
    {
        switch ($this->getUserTypeByRequestUri()) {
            case self::SYSTEM_USER:
                return $this->container->get('barbon.hosted_api.security.provider.system_user_provider');
            case self::AGENT_USER:
                return $this->container->get('barbon.hosted_api.security.provider.agent_user_provider');   
            case self::LANDLORD_USER:
                return $this->container->get('barbon.hosted_api.security.provider.landlord_user_provider');
            default:
                // for some reason, the dev profiler routes come in here
                return $this->container->get('barbon.hosted_api.security.provider.system_user_provider');
        }
    }

    /**
     * Determine the type of User that is needed for the current request, based on the URL 
     *
     * @return string
     */
    private function getUserTypeByRequestUri()
    {
        if (is_null($this->requestStack->getCurrentRequest())) {
            return;
        }
        
        $urlStack = explode('/', $this->requestStack->getCurrentRequest()->getRequestUri());
        $userTypes = explode('|', self::USER_TYPES);

        foreach ($urlStack as $urlStub) {
            if (in_array($urlStub, $userTypes)) {
                return $urlStub;
            }
        }
    }
}
