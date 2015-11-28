<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

namespace Barbon\HostedApi\SecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;
use Barbon\HostedApi\SecurityBundle\Firewall\AsnUsernamePasswordFormAuthenticationListener;

/**
 * Security Controller.
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login")
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
    
        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } 
        elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } 
        else {
            $error = '';
        }
    
        // last asn and username entered by the user
        $lastAsn = (null == $session) ? '' : $session->get(AsnUsernamePasswordFormAuthenticationListener::LAST_ASN);
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);
    
        return array(
            'last_asn' => $lastAsn,
            'last_username' => $lastUsername,
            'error'         => $error
        );   
    }

    /**
     * @Route("/login_check")
     *
     * @return void
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout")
     *
     * @return void
     */
    public function logoutAction()
    {
    }
}