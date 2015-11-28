<?php

namespace Barbondev\IRISSDK\SystemApplication\Tat;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class TatClient
 *
 * @package Barbondev\IRISSDK\SystemApplication\Tat
 * @author Paul Swift <paul.swift@barbon.com>
 *
 * @method \Barbondev\IRISSDK\SystemApplication\Tat\Model\TatStatus getTatStatus(array $args = array())
 */
class TatClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return TatClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/systemapplication-tat-v%s.php',
            ))
            ->build()
        ;
    }
}