<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\Authentication\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Barbon\HostedApi\SecurityBundle\Firewall\AsnUsernamePasswordFormAuthenticationListener;

/**
 * Custom handler to handle successful login.
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */ 
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor
     *
     * @param SecurityContext $security
     * @param Router $router
     *
     */
    public function __construct(SecurityContext $security, Router $router)
    {
        $this->security = $security;
        $this->router = $router;
    }
    
    /**
     * Triggered on successful login.
     *
     * @param Request $request
     * @param TokenInterface $token
     * 
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // clean up some session data used at login
        $request->getSession()->remove(AsnUsernamePasswordFormAuthenticationListener::LAST_ASN);

        $targetPathString = sprintf('_security.%s.target_path', $token->getProviderKey());

        // if we were redirected to login after accessing a secure url, return back to the url
        if ($request->getSession()->has($targetPathString)) {
            $targetUrl = $request->getSession()->get($targetPathString);

            // clean the session
            $request->getSession()->remove($targetPathString);
        } 
        // otherwise go to root
        else {
            $targetUrl = '/';
        }

        return new RedirectResponse($targetUrl);
    }
}
