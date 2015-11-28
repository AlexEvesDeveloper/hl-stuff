<?php

namespace Barbondev\IRISSDK\Landlord\Landlord;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class LandlordClient
 *
 * @package Barbondev\IRISSDK\Landlord
 * @author Paul Swift <paul.swift@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Landlord\Landlord\Model\Landlord getLandlord(array $args = array())
 * @method \Guzzle\Http\Message\Response updateLandlord(array $args = array())
 */
class LandlordClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return LandlordClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/landlord-v%s.php',
            ))
            ->build()
        ;
    }
}