<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Custom Token to handle the ASN number on top of the UsernamePasswordToken
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */ 
class AsnUsernamePasswordToken extends UsernamePasswordToken
{
    /**
     * @var string
     */
    private $asn;

    /**
     * Constructor.
     *
     * @param string|object            $user        The username (like a nickname, email address, etc.), or a UserInterface instance or an object implementing a __toString method.
     * @param string                   $credentials This usually is the password of the user
     * @param string                   $providerKey The provider key
     * @param RoleInterface[]|string[] $roles       An array of roles
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($user, $credentials, $providerKey, array $roles = array(), $asn)
    {
        parent::__construct($user, $credentials, $providerKey, $roles);

        $this->asn = $asn;
    }

    /**
     * get $asn
     *
     * @return string
     */
    public function getAsn()
    {
        return $this->asn;
    }    
}
