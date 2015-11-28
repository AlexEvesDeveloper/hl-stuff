<?php

namespace Barbondev\IRISSDK\Utility\AddressFinder;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class RentAffordabilityClient
 *
 * @package Barbondev\IRISSDK\AddressFinder\AddressFinderClient
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Utility\AddressFinder\Model\PafAddress findAddress(array $args = array())
 */
class AddressFinderClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return AddressFinderClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/address-finder-v%s.php',
            ))
            ->build()
        ;
    }
}