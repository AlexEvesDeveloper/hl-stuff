<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference;

use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Event\NewReferenceFailureEvent;
use Barbon\HostedApi\AppBundle\Event\NewReferenceFinishEvent;
use Barbon\HostedApi\AppBundle\Traits\SessionModelRetrieverTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/failure")
 */
class FailureController extends Controller
{
    use SessionModelRetrieverTrait;

    /**
     * @Route()
     * @Method({"GET"})
     * @Template()
     *
     * @param Request $request
     * @return array
     * @throws \Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException
     */
    public function indexAction(Request $request)
    {
        $case = $this->getCase($request->getSession());

        // Dispatch the failed payment event.
        $this->get('event_dispatcher')->dispatch(
            NewReferenceEvents::NEW_REFERENCE_FAILURE,
            new NewReferenceFailureEvent($case)
        );

        // Dispatch the finish event.
        $this->get('event_dispatcher')->dispatch(
            NewReferenceEvents::NEW_REFERENCE_FINISH,
            new NewReferenceFinishEvent($case)
        );

        return array();
    }
}