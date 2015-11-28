<?php

namespace Barbondev\IRISSDK\Utility\RentAffordability;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class RentAffordabilityClient
 *
 * @package Barbondev\IRISSDK\Utility\RentAffordability
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability checkRentAffordability(array $args = array())
 * @method \Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability checkTenantRentAffordability(array $args = array())
 * @method \Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability checkGuarantorRentAffordability(array $args = array())
 */
class RentAffordabilityClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return RentAffordabilityClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/rent-affordability-v%s.php',
            ))
            ->build()
        ;
    }
}