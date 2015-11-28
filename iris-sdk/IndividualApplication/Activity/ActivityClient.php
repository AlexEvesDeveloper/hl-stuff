<?php

namespace Barbondev\IRISSDK\IndividualApplication\Activity;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class ActivityClient
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Activity
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Guzzle\Common\Collection getActivities(array $args = array())
 */
class ActivityClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return ActivityClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/activity-v%s.php',
            ))
            ->build()
        ;
    }
}