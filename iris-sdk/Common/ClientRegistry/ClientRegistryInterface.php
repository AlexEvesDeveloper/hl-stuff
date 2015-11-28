<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;

/**
 * Class ClientRegistryInterface
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ClientRegistryInterface
{
    /**
     * Add a context
     *
     * @param ContextInterface $context
     * @return $this
     */
    public function addContext(ContextInterface $context);

    /**
     * Get a context by name
     *
     * @param string $name
     * @return ContextInterface
     * @throws \Barbondev\IRISSDK\Common\ClientRegistry\Exception\ContextDoesNotExistException
     */
    public function getContextByName($name);
}