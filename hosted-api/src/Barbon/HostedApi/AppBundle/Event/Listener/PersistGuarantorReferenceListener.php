<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Event\NewGuarantorReferenceEvent;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\SignaturePreference;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Process and persist a Guarantor reference to an Application
 *
 * Class PersistGuarantorReferenceListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class PersistGuarantorReferenceListener
{
    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param IrisEntityManager $irisEntityManager
     * @param RequestStack $requestStack
     */
    public function __construct(IrisEntityManager $irisEntityManager, RequestStack $requestStack)
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @param NewGuarantorReferenceEvent $event
     */
    public function persistGuarantorReference(NewGuarantorReferenceEvent $event)
    {
        $case = $event->getCase();
        $application = $event->getApplication();
        $guarantor = $event->getGuarantor();

        // We don't gather this, but IRIS requires it, so always set to this default.
        $guarantor->setSignaturePreference(SignaturePreference::SCAN_DECLARATION);
        $guarantor->setProduct($application->getProduct());

        // Persist the Guarantor to IRIS, attaching it to the Application.
        $this->irisEntityManager->persist($guarantor, array(
            'applicationId' => $application->getApplicationId()
        ));

        // Put the newly persisted Guarantor into the session
        $this->requestStack->getCurrentRequest()->getSession()->set('submitted-guarantor', serialize($guarantor));

        // Add the Guarantor to the Application.
        $application->setGuarantors(array($guarantor));

        // Add the newly updated Application back into the Case.
        $case->setApplications(array($application));

        // Explicitly set the caseId into the session
        $case->setCaseId($application->getCaseId());

        // Put the newly updated Case back into the session.
        $this->requestStack->getCurrentRequest()->getSession()->set('submitted-case', serialize($case));
    }
}