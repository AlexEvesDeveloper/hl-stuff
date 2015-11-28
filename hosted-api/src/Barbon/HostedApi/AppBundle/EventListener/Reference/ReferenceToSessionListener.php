<?php

namespace Barbon\HostedApi\AppBundle\EventListener\Reference;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Listener to capture any posted data to the given form, and store in the session
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceToSessionListener
{
    /**
     * @var FormTypeInterface
     */
    private $formType;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param FormFactory $formFactory
     * @param FormTypeInterface $formType
     *
     * @return void
     */
    public function __construct(FormFactory $formFactory, FormTypeInterface $formType)
    {
        $this->formFactory = $formFactory;
        $this->formType = $formType;
    }

    /**
     * Triggered on every request.
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        // if the request contains a key equal to the name of the form in this class...
        $acceptableContentTypes = array_map('strtolower', $request->getAcceptableContentTypes());
        if (
            $request->request->has($this->formType->getName()) &&
            in_array('text/html', $acceptableContentTypes) &&
            ! $request->isXmlHttpRequest()
        ) {
            // store the request so we can retrieve the posted form data later, if necessary
            $request->getSession()->set($this->formType->getName(), serialize($request));

            // whilst we are here, store the target URL of the form, in case we are redirected to login page
            // $request->getSession()->set('_security.asn_secured.target_path', $request->getUri());
        }
    }
}
