<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\Multi;

use Barbon\HostedApi\AppBundle\Event\ConfirmMultiReferenceEvent;
use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;
use Barbon\HostedApi\AppBundle\Traits\IrisModelRetrieverTrait;
use Barbon\HostedApi\AppBundle\Traits\SessionModelRetrieverTrait;
use Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\AbstractConfirmationController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
* @Route(service="barbon.hosted_api.landlord.reference.controller.new_reference.multi.confirmation_controller")
*/
class ConfirmationController extends AbstractConfirmationController
{
    use SessionModelRetrieverTrait, IrisModelRetrieverTrait {
        SessionModelRetrieverTrait::getCase insteadof IrisModelRetrieverTrait;
    }

    /**
     * @Route("/confirmation")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @return array
     * @throws CaseNotSubmittedException
     */
    public function indexAction(Request $request)
    {
        $case = $this->getCase($request->getSession());
        $applications = $this->getApplications($this->irisEntityManager, $case->getCaseId());

        $form = $this->buildTenancyAgreementForm(array_reverse($applications));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Update IRIS with the submitted marketing preferences.
            foreach ($applications as $application) {
                $this->persistMarketingPreferences($application, $case->getCaseId());
            }

            // Dispatch the confirmed multi reference event.
            $this->confirmationEvent->setCase($case)->setReferences($applications);
            $this->eventDispatcher->dispatch(
                NewReferenceEvents::MULTI_REFERENCE_CONFIRMED,
                $this->confirmationEvent
            );

            // Something listening to the confirm_guarantor_reference_event has generated a response, most likely a redirect to payment.
            if ($this->confirmationEvent->getResponse() instanceof Response) {
                return $this->confirmationEvent->getResponse();
            }

            // If no payment redirect has been triggered, go straight to the summary page of the Case.
            return $this->redirectToRoute('barbon_hostedapi_landlord_reference_cases_view_index', array(
                'caseId' => $case->getCaseId()
            ));
        }

        return array(
            'form' => $form->createView(),
            'case' => $case
        );
    }
}
