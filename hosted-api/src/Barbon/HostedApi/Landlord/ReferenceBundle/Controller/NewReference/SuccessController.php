<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference;

use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Event\NewReferenceFinishEvent;
use Barbon\HostedApi\AppBundle\Event\NewReferenceSuccessEvent;
use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;
use Barbon\HostedApi\AppBundle\Traits\SessionModelRetrieverTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/success")
 */
class SuccessController extends Controller
{
    use SessionModelRetrieverTrait;

    /**
     * @Route()
     * @Method({"GET"})
     * @Template()
     *
     * @param Request $request
     * @return array
     * @throws CaseNotSubmittedException
     */
    public function indexAction(Request $request)
    {
        $case = $this->getCase($request->getSession());

        // Dispatch the successful payment event.
        $this->get('event_dispatcher')->dispatch(
            NewReferenceEvents::NEW_REFERENCE_SUCCESS,
            new NewReferenceSuccessEvent($case)
        );

        // Dispatch the finish event.
        $this->get('event_dispatcher')->dispatch(
            NewReferenceEvents::NEW_REFERENCE_FINISH,
            new NewReferenceFinishEvent($case)
        );

        return $this->redirect($this->generateUrl('barbon_hostedapi_landlord_reference_cases_view_index', array(
            'caseId' => $case->getCaseId()
        )));
    }
}