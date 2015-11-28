<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\NewReference;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\CompletionMethod;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\SignaturePreference;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route(service="barbon.hosted_api.agent.reference.controller.new_reference.new_controller")
 */
final class NewController extends Controller
{
    /**
     * Page form
     *
     * @var FormTypeInterface
     */
    private $formType;

    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param FormTypeInterface $formType
     */
    public function __construct(IrisEntityManager $irisEntityManager, FormTypeInterface $formType)
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->formType = $formType;
    }

    /**
     * Reference purchase summary
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
        $previouslyPostedData = null;

        // if we are not posting new data, and a request for $this->formType is stored in the session, prepopulate the form with the stored request
        $storedRequest = unserialize($request->getSession()->get($this->formType->getName()));
        if ($request->isMethod('GET') && $storedRequest instanceof Request) {
            $previouslyPostedData = $this->createForm($this->formType)->handleRequest($storedRequest)->getData();
        }

        $form = $this->createForm($this->formType, $previouslyPostedData);

        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                // Persist the case to IRIS

                /** @var ReferencingCase $case */
                $case = $form->getData()['case'];
                $this->irisEntityManager->persist($case);

                /** @var ReferencingApplication $application */
                foreach ($case->getApplications() as $application) {

                    // Always default
                    $application->setSignaturePreference(SignaturePreference::SCAN_DECLARATION);
                    
                    $this->irisEntityManager->persist($application, array(
                        'caseId' => $case->getCaseId(),
                    ));

                    // Persist each guarantor of the application
                    if (null !== $application->getGuarantors()) {
                        foreach ($application->getGuarantors() as $guarantor) {
                            $this->irisEntityManager->persist($guarantor, array(
                                'applicationId' => $application->getApplicationId(),
                            ));
                        }
                    }
                }

                $request->getSession()->set('submitted-case', serialize($case));

                // Send the user to the success page
                return $this->redirect(
                    $this->generateUrl('barbon_hostedapi_agent_reference_newreference_tenancyagreement_index'),
                    301
                );
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}