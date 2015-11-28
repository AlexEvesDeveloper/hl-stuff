<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry\Context;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ClientAlreadyExistsException;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ClientDoesNotExistException;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ClientNameInWrongFormatException;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\FailedToResolveClientException;

/**
 * Class AbstractContext
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry\Context
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractContext implements ContextInterface
{
    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $clients = array();

    /**
     * Constructor
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
        $this->initialise();
    }

    /**
     * Initialise context by adding all clients
     *
     * @return mixed
     */
    abstract protected function initialise();

    /**
     * {@inheritdoc}
     */
    public function addClient($name, \Closure $factory)
    {
        if (isset($this->factories[$name])) {
            throw new ClientAlreadyExistsException(
                sprintf('Client already exists in context with the name %s', $name)
            );
        }

        $this->factories[$name] = $factory;

        return $this;
    }

    /**
     * Proxy to a getClientByName method using format $context->get*Client()
     *
     * @param string $method
     * @param array $args
     * @throws \Barbondev\IRISSDK\Common\ClientRegistry\Exception\ClientNameInWrongFormatException
     * @return \Barbondev\IRISSDK\AbstractClient
     */
    public function __call($method, $args)
    {
        if (preg_match('/^get([a-zA-Z0-9_]+)Client$/', $method, $matches)) {
            return $this->getClientByName(lcfirst($matches[1]));
        }

        throw new ClientNameInWrongFormatException(
            sprintf('Client method name %s is in wrong format - must be get*Client', $method)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getClientByName($name)
    {
        // Has the client already been resolved? If so, return it
        if (isset($this->clients[$name])) {
            if ($this->clients[$name] instanceof AbstractClient) {
                return $this->clients[$name];
            }
        }

        // Does the factory exist?
        if ( ! isset($this->factories[$name])) {
            throw new ClientDoesNotExistException(
                sprintf('Client with the name %s does not exist in %s context', $name, $this->getName())
            );
        }

        // Check that the factory is closure
        if ( ! ($this->factories[$name] instanceof \Closure)) {
            throw new FailedToResolveClientException(
                sprintf('Factory is not \Closure for client name %s in %s context', $name, $this->getName())
            );
        }

        // Resolve client
        $client = $this->factories[$name]($this->parameters);

        // Is the factory returned client of the expected type?
        if ( ! ($client instanceof AbstractClient)) {
            throw new FailedToResolveClientException(
                sprintf('Factory returned %s where Barbondev\IRISSDK\AbstractClient was expected', get_class($client))
            );
        }

        // Return the resolved client
        return $this->clients[$name] = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}