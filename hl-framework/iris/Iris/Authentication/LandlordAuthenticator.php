<?php

namespace Iris\Authentication;

/**
 * Class LandlordAuthenticator
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LandlordAuthenticator extends AbstractAuthenticator
{
    /**
     * Authenticate landlord, passing back the authorisation token
     *
     * @param string $email
     * @param string $password
     * @return AuthorizationTokenInterface
     */
    public function authenticate($email, $password)
    {
        $authorization = $this
            ->irisClientRegistry
            ->getSystemContext()
            ->getLandlordClient()
            ->authenticate(array(
                'email' => $email,
                'password' => $password,
            ))
        ;

        return new LandlordAuthorizationToken($authorization->getConsumerKey(), $authorization->getConsumerSecret());
    }
}