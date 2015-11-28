<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\Applications;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Common\Model\Progress;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationCollection;
use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/view", service="barbon.hosted_api.agent.reference.controller.applications.view_controller")
 */
final class ViewController extends Controller
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
     * Application overview
     *
     * @Route("/{applicationId}")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param $applicationId
     * @return Response
     */
    public function indexAction($applicationId)
    {
        try {
            /** @var ReferencingApplication $application */
            $application = $this->irisEntityManager->find(new ReferencingApplication(), array(
                'applicationId' => $applicationId,
            ));
            
            /** @var Progress $progress */
            $progress = $this->irisEntityManager->find(new Progress(), array(
                'applicationId' => $applicationId,
            ));
        }
        catch(NotFoundException $ex) {
            // Case could not be found
            throw $this->createNotFoundException(sprintf('Application with applicationId "%s" could not be found', $applicationId));
        }

        try {
            /** @var ReferencingCase $case */
            $case = $this->irisEntityManager->find(new ReferencingCase(), array(
                'caseId' => $application->getCaseId(),
            ));
        }
        catch(NotFoundException $ex) {
            // Case could not be found
            throw $this->createNotFoundException(sprintf('Case with caseId "%s" could not be found', $application->getCaseId()));
        }

        return array(
            'caseId' => $application->getCaseId(),
            'case' => $case,
            'application' => $application,
            'progress' => $progress,
        );
    }
}
