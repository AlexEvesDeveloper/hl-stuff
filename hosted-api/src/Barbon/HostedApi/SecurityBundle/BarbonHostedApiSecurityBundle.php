<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Barbon\HostedApi\SecurityBundle\DependencyInjection\Factory\AsnUsernamePasswordFactory;

/**
 * Bundle class file
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class BarbonHostedApiSecurityBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new AsnUsernamePasswordFactory());
    }
}