<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\Guarantor;

use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;
use Barbon\HostedApi\AppBundle\Traits\IrisModelRetrieverTrait;
use Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\AbstractConfirmationController;
use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route(service="barbon.hosted_api.landlord.reference.controller.new_reference.guarantor.confirmation_controller")
 */
class ConfirmationController extends AbstractConfirmationController
{
    use IrisModelRetrieverTrait;

    /**
     * @Route("/confirmation/{applicationId}")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param $applicationId
     * @return array
     * @throws CaseNotSubmittedException
     * @throws NotFoundException
     */
    public function indexAction(Request $request, $applicationId)
    {
        // Get the recently submitted Guarantor from the session...
        if (false === ($guarantor = unserialize($request->getSession()->get('submitted-guarantor')))) {
            throw new CaseNotSubmittedException('submitted-guarantor could not be retrieved from the session.');
        }

        // ...and we'll need the Guarantor's Case and Application.
        $application = $this->getApplication($this->irisEntityManager, $applicationId);
        $case = $this->getCase($this->irisEntityManager, $application->getCaseId());

        $form = $this->buildTenancyAgreementForm(array($guarantor));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Update IRIS with the submitted marketing preferences.
            $this->persistMarketingPreferences($guarantor, $case->getCaseId());

            // Dispatch the confirmed guarantor reference event.
            $this->confirmationEvent->setReference($guarantor);
            $this->eventDispatcher->dispatch(
                NewReferenceEvents::INDIVIDUAL_REFERENCE_CONFIRMED,
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
            'case' => $case,
            'guarantor' => $guarantor
        );
    }
}