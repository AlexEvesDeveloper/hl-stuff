<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Event\NewMultiReferenceEvent;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\SignaturePreference;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Process and persist a brand new Reference: Case, Applicant(s), Guarantor(s)
 *
 * Class PersistMultiReferenceListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class PersistMultiReferenceListener
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
     * @param NewMultiReferenceEvent $event
     */
    public function persistMultiReference(NewMultiReferenceEvent $event)
    {
        $case = $event->getCase();

        // Persist the Case to IRIS.
        $this->irisEntityManager->persist($case);

        // Process each Tenant Application.
        foreach ($case->getApplications() as $application) {
            // We don't gather this, but IRIS requires it, so always set to this default.
            $application->setSignaturePreference(SignaturePreference::SCAN_DECLARATION);

            // Persist the Applicant to IRIS.
            $this->irisEntityManager->persist($application, array('caseId' => $case->getCaseId()));

            // If present, process each Guarantor of the Application.
            if (null !== $application->getGuarantors()) {
                foreach ($application->getGuarantors() as $guarantor) {
                    // We don't gather this, but IRIS requires it, so always set to this default.
                    $guarantor->setSignaturePreference(SignaturePreference::SCAN_DECLARATION);

                    // Persist the Guarantor to IRIS.
                    $this->irisEntityManager->persist($guarantor, array('applicationId' => $application->getApplicationId()));
                }
            }
        }

        // Put the newly updated Case back into the session.
        $this->requestStack->getCurrentRequest()->getSession()->set('submitted-case', serialize($case));
    }
}