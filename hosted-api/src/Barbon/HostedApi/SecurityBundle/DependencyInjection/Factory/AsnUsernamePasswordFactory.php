<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\DependencyInjection\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Responsible for building the Authentication Listeners/Factories
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class AsnUsernamePasswordFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string $id
     * @param array $config
     * @param UserProviderInterface $userProvider
     * @param string $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'barbon.hosted_api.security.firewall.asn_username_password_authentication_provider.' . $id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('barbon.hosted_api.security.firewall.asn_username_password_authentication_provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'barbon.hosted_api.security.firewall.asn_username_password_authentication_listener.' . $id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('barbon.hosted_api.security.firewall.asn_username_password_authentication_listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'asn';
    }

    /**
     * Interface method
     *
     * @return void
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
