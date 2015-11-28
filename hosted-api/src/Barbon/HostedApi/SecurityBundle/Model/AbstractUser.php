<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Represent an abstract user
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
abstract class AbstractUser implements UserInterface, EquatableInterface
{
    /**
     * defaults to represent the consumer key
     *
     * @var string
     */
    protected $username;

    /**
     * defaults to represent the consumer secret
     *
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $vendorKey;

    /**
     * @var string
     */
    protected $vendorSecret;

    /**
     * @var string
     */
    protected $consumerKey;

    /**
     * @var string
     */
    protected $consumerSecret;

    /**
     * @var array
     */
    protected $roles;

    /**
     * Constructor.
     *
     * @param string $vendorKey
     * @param string $vendorSecret
     * @param string $username
     * @param string $password
     * @internal param array $roles
     */
    public function __construct($vendorKey, $vendorSecret, $username, $password)
    {
        $this->vendorKey = $vendorKey;
        $this->vendorSecret = $vendorSecret;
        $this->username = $username;
        $this->password = $password;

        $this->setConsumerKey($username);
        $this->setConsumerSecret($password);
    }

    /**
     * Return a role specfic to the concrete user type
     *
     * @return array
     */
    abstract public function getRoles();

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getVendorKey()
    {
        return $this->vendorKey;
    }

    /**
     * @return string
     */
    public function getVendorSecret()
    {
        return $this->vendorSecret;
    }

    /**
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @return $this
     */
    public function setVendorKey($vendorKey)
    {
        $this->vendorKey = $vendorKey;
    }

    /**
     * @return $this
     */
    public function setVendorSecret($vendorSecret)
    {
        $this->vendorSecret = $vendorSecret;
    }

    /**
     * @return $this
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @return $this
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * @return void
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if ( ! $user instanceof AbstractUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}