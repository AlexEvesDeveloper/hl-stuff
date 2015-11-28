<?php

namespace Barbon\HostedApi\Agent\DebugBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DebugController extends Controller
{
    /**
     * @Route("/post-reference")
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array(
            'hostname' => $request->getHost(),
            'scheme' => $request->getScheme(),
            'basepath' => $request->getBaseUrl(),
        );
    }
}
