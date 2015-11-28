<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\NewReference;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\AbstractReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Barbon\HostedApi\AppBundle\Form\Reference\Type\TenancyAgreementType;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\TenancyAgreement;
use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;

/**
 * @Route("/tenancy-agreement", service="barbon.hosted_api.agent.reference.controller.new_reference.tenancy_agreement_controller")
 */
final class TenancyAgreementController extends Controller
{
    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     */
    public function __construct(IrisEntityManager $irisEntityManager)
    {
        $this->irisEntityManager = $irisEntityManager;
    }

    /**
     * Success page after reference purchase
     *
     * @Route()
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @return array
     * @throws CaseNotSubmittedException
     */
    public function indexAction(Request $request)
    {
        // Get the submitted case
        /** @var ReferencingCase $case */
        $case = unserialize($request->getSession()->get('submitted-case'));
        
        if (false === $case) {
            // Failed to retrieve case from session
            throw new CaseNotSubmittedException('submitted-case could not be retrieved from the session.');
        }

        // Flatten application/guarantor hierarchy in depth first search order
        // todo: only needs doing for applications who are completing now
        $applications = array();
        foreach ($case->getApplications() as $application) {
            $applications[] = $application;

            if (null !== $application->getGuarantors()) {
                foreach ($application->getGuarantors() as $guarantor) {
                    $applications[] = $guarantor;
                }
            }
        }

        $tenancyAgreement = new TenancyAgreement();
        $tenancyAgreement->setApplications($applications);

        // Create form, with flattened data
        $form = $this->createForm(new TenancyAgreementType(), $tenancyAgreement);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Persist each application/guarantor model with the updated marketing preferences

            /** @var AbstractReferencingApplication $application */
            foreach ($applications as $application) {
                $this->irisEntityManager->persist($application, array(
                    'caseId' => $case->getCaseId(),
                    'applicationId' => $application->getApplicationId(),
                ));
            }

            // Submit the entire case
            $case->submit($this->irisEntityManager->getClient());
            
            // Flush the case/applications from the session for the next reference
            $request->getSession()->remove('submitted-case');

            return $this->redirect($this->generateUrl(
                'barbon_hostedapi_agent_reference_cases_view_index',
                array('caseId' => $case->getCaseId())
            ));
        }

        return array(
            'form' => $form->createView()
        );
    }
}