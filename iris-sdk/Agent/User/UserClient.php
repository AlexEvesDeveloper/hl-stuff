<?php

namespace Barbondev\IRISSDK\Agent\User;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class BranchClient
 *
 * @package Barbondev\IRISSDK\Agent\Branch
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Agent\User\Model\User getUser(array $args = array())
 * @method \Guzzle\Http\Message\Response updateUser(array $args = array())
 * @method \Guzzle\Http\Message\Response deleteUser(array $args = array())
 */
class UserClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return UserClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/user-v%s.php',
            ))
            ->build()
        ;
    }
}