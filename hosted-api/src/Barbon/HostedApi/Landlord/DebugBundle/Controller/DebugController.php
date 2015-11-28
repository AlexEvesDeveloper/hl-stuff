<?php

namespace Barbon\HostedApi\Landlord\DebugBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugController extends Controller
{
    /**
     * @Route("/request-auth")
     * @Method({"GET"})
     * @Template()
     */
    public function requestAuthAction(Request $request)
    {
        return array(
            'hostname' => $request->getHost(),
            'scheme' => $request->getScheme(),
            'basepath' => $request->getBaseUrl(),
        );
    }

    /**
     * @Route("/reference-callback")
     * @Method({"GET", "POST"})
     */
    public function referenceCallbackAction(Request $request)
    {
        // Get monolog logger
        $logger = $this->get('logger');

        // Log method, GET and POST params at debug level
        $logger->debug('Landlord referencing callback', array(
            'method' => $request->getMethod(),
            'get' => $request->query,
            'post' => $request->request
        ));

        // Return a 200 OK
        return new Response();
    }

    /**
     * @Route("/reference-success")
     * @Method({"GET"})
     * @Template()
     */
    public function referenceSuccessAction(Request $request)
    {
    }

    /**
     * @Route("/reference-failure")
     * @Method({"GET"})
     * @Template()
     */
    public function referenceFailureAction(Request $request)
    {
    }
}
