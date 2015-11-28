<?php

namespace Iris\Authentication;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;

/**
 * Class AbstractAuthentication
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * @var ClientRegistry
     */
    protected $irisClientRegistry;

    /**
     * Set IRIS API client registry
     *
     * @param ClientRegistry $irisClientRegistry
     * @return $this
     */
    public function setIrisClientRegistry(ClientRegistry $irisClientRegistry)
    {
        $this->irisClientRegistry = $irisClientRegistry;

        return $this;
    }
}