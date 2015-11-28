<?php

namespace Barbon\HostedApi\AppBundle\Service\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Manages the interaction with the MAC value
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class MacManager
{
    /**
     * @var string
     */
    protected $mac;

    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Constructor
     * 
     * @param RequestStack $requestStack
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Generate an encrypted string (MAC) using the username and credentials of the current token.
     *
     * @return $this
     */
    public function generate()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $this->mac = hash('sha256', sprintf('%s%s%s', $user->getUsername(), $user->getPassword(), microtime()));

        return $this;
    }

    /**
     * Return the MAC. Generate one if haven't done so already
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }
}
