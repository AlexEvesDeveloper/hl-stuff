<?php

namespace Barbondev\IRISSDK;

use Guzzle\Service\Client;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Guzzle\Common\Collection;

/**
 * Class AbstractClient
 *
 * @package Barbondev\IRISSDK
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractClient extends Client
{
    /**
     * Constructor
     *
     * @param \Guzzle\Common\Collection $config
     */
    public function __construct(Collection $config)
    {
        parent::__construct($config->get(ClientOptions::BASE_URL), $config);
    }

    /**
     * Alias commands with direct method calls
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return parent::__call(ucfirst($method), $args);
    }
}