<?php

namespace Iris\Authentication;

/**
 * Class LandlordAuthorizationToken
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LandlordAuthorizationToken extends AbstractAuthorizationToken
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'landlord';
    }
}