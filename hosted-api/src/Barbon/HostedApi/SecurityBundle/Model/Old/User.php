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
 * Represent a User 
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @var string
     */
    private $asn;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $consumerKey;

    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * @var string
     */
    private $branchUuid;

    /**
     * Constructor.
     *
     * @param string $asn
     * @param string $username
     * @param string $password
     * @param array $roles
     * @param string $consumerKey
     * @param string $consumerSecret
     * @param string|null $branchUuid
     */
    public function __construct(
        $asn,
        $username,
        $password,
        array $roles,
        $consumerKey,
        $consumerSecret,
        $branchUuid = null
    )
    {
        $this->asn = $asn;
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->branchUuid = $branchUuid;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

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
    public function getAsn()
    {
        return $this->asn;
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
     * @return string
     */
    public function getBranchUuid()
    {
        return $this->branchUuid;
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
        if ( ! $user instanceof WebserviceUser) {
            return false;
        }

        if ($this->asn !== $user->getAsn()) {
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