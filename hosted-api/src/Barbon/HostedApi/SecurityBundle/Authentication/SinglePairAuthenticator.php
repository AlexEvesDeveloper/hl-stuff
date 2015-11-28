<?php

namespace Barbon\HostedApi\SecurityBundle\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class SinglePairAuthenticator extends AbstractAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        $credentials = array(
            'systemKey' => $request->request->get('systemKey'),
            'systemSecret' => $request->request->get('systemSecret')
        );

        // no missing keys allowed
        foreach ($credentials as $key => $credential) {
            if ( ! $credential) {
                throw new BadCredentialsException(sprintf('API key missing from POST: %s missing', $key));
            }
        }

        return new PreAuthenticatedToken('anon.', $credentials, $providerKey);
    }
}
