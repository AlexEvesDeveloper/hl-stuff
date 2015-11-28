<?php

namespace Barbon\HostedApi\AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BarbonHostedApiAppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('Brand/services/controllers.yml');
        $loader->load('Brand/services/services.yml');
        $loader->load('Lookup/services/controllers.yml');
        $loader->load('Authentication/services/controllers.yml');
        $loader->load('Authentication/services/services.yml');
        $loader->load('Reference/services/form_types.yml');
        $loader->load('Reference/services/event_listeners.yml');
        $loader->load('Reference/services/services.yml');
        $loader->load('Common/services/form_types.yml');
        $loader->load('Common/services/iris_rest_client.yml');
        $loader->load('Common/services/lookup_services.yml');
        $loader->load('Common/services/events.yml');
        $loader->load('Common/services/event_listeners.yml');
    }
}
