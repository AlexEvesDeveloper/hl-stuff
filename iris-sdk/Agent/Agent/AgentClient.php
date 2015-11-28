<?php

namespace Barbondev\IRISSDK\Agent\Agent;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class AgentClient
 *
 * @package Barbondev\IRISSDK\Agent\Agent
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Agent\Agent\Model\Agent getAgent(array $args = array())
 * @method \Guzzle\Http\Message\Response updateAgent(array $args = array())
 * @method \Barbondev\IRISSDK\Agent\Agent\Model\AgentUser createAgentUser(array $args = array())
 */
class AgentClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return AgentClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/agent-v%s.php',
            ))
            ->build()
        ;
    }
}