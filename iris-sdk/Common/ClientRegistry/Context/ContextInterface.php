<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry\Context;

use Barbondev\IRISSDK\AbstractClient;
use Psr\Log\LoggerInterface;

/**
 * Class ContextInterface
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry\Context
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ContextInterface
{
    /**
     * Get context name
     *
     * @return string
     */
    public function getName();

    /**
     * Add a client (via factory closure) to the context
     *
     * @param string $name
     * @param callable $factory
     * @return $this
     * @throws \Barbondev\IRISSDK\Common\ClientRegistry\Exception\ClientAlreadyExistsException
     */
    public function addClient($name, \Closure $factory);

    /**
     * Get a client by name
     *
     * @param string $name
     * @return AbstractClient
     */
    public function getClientByName($name);

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters);

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters();
}