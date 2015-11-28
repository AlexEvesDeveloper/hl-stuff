<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\NewReference;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/validate", service="barbon.hosted_api.agent.reference.controller.new_reference.validate_controller")
 */
final class ValidateController extends Controller
{
    /**
     * Page form
     *
     * @var FormTypeInterface
     */
    private $formType;

    /**
     * Constructor
     *
     * @param FormTypeInterface $formType
     */
    public function __construct(FormTypeInterface $formType)
    {
        $this->formType = $formType;
    }
    
    /**
     * Validate a page and render just the form
     *
     * @Route()
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm($this->formType);

        try {
            $form->submit($request);
        }
        catch (ClientException $e) {
            // This type of exception should be absorbed as it'll most likely be the IRIS API being confused by incomplete data
            // todo: probably ought to do some logging though as it shouldn't happen
        }
        catch (ServerException $e) {
            // This type of exception should be absorbed as it'll most likely be the IRIS API being confused by incomplete data
            // todo: probably ought to do some logging though as it shouldn't happen
        }

        return array(
            'form' => $form->createView()
        );
    }
}