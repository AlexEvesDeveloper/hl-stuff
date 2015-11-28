<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route(service="barbon.hosted_api.landlord.authentication.controller.login_controller")
 */
class LoginController extends Controller
{
    /**
     * @Route("/login")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            'error' => $error,
            'last_username' => $lastUsername
        );
    }

    /**
     * @Route("/login_check")
     * @Method({"POST"})
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout")
     * @Method({"GET"})
     */
    public function logoutAction()
    {
    }
}
