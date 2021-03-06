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
 * Represent a landlord user
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class LandlordUser extends AbstractUser
{
    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array('ROLE_LANDLORD');
    }
}
