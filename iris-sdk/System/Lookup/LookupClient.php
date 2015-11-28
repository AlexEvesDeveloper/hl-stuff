<?php

namespace Barbondev\IRISSDK\System\Lookup;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class LookupClient
 *
 * @package Barbondev\IRISSDK\System\Lookup
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\System\Lookup\Model\Lookup getLookup(array $args = array())
 */
class LookupClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return LookupClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/lookup-v%s.php',
            ))
            ->build()
        ;
    }
}