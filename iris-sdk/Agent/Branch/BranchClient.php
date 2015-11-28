<?php

namespace Barbondev\IRISSDK\Agent\Branch;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class BranchClient
 *
 * @package Barbondev\IRISSDK\Agent\Branch
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Guzzle\Common\Collection getBranches(array $args = array())
 * @method \Barbondev\IRISSDK\Agent\Branch\Model\Branch getBranch(array $args = array())
 * @method \Guzzle\Http\Message\Response updateBranch(array $args = array())
 * @method \Guzzle\Common\Collection getBranchUsers(array $args = array())
 * @method \Barbondev\IRISSDK\Agent\User\Model\User createBranchUser(array $args = array())
 */
class BranchClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return BranchClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/branch-v%s.php',
            ))
            ->build()
        ;
    }
}