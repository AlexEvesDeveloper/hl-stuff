<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ContextAlreadyExistsException;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ContextDoesNotExistException;
use Barbondev\IRISSDK\Common\ClientRegistry\Exception\ContextNameInWrongFormatException;

/**
 * Class ClientRegistry
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Common\ClientRegistry\Context\SystemContext getSystemContext()
 * @method \Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext getAgentContext()
 * @method \Barbondev\IRISSDK\Common\ClientRegistry\Context\LandlordContext getLandlordContext()
 */
class ClientRegistry implements ClientRegistryInterface
{
    /**
     * @var array
     */
    protected $contexts = array();

    /**
     * Proxy to a getContextByName method using format $context->get*Context()
     *
     * @param string $method
     * @param array $args
     * @return ContextInterface
     * @throws Exception\ContextNameInWrongFormatException
     */
    public function __call($method, $args)
    {
        if (preg_match('/^get([a-zA-Z0-9_]+)Context$/', $method, $matches)) {
            return $this->getContextByName(lcfirst($matches[1]));
        }

        throw new ContextNameInWrongFormatException(
            sprintf('Context method name %s is in wrong format - must be get*Context', $method)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addContext(ContextInterface $context)
    {
        if (isset($this->contexts[$context->getName()])) {
            throw new ContextAlreadyExistsException(
                sprintf('Context already exists in client registry with the name %s', $context->getName())
            );
        }

        $this->contexts[$context->getName()] = $context;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextByName($name)
    {
        if ( ! isset($this->contexts[$name])) {
            throw new ContextDoesNotExistException(
                sprintf('Context does not exist with the name %s', $name)
            );
        }

        return $this->contexts[$name];
    }
}