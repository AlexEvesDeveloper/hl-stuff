<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\Applications;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Report;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;

/**
 * @Route(service="barbon.hosted_api.agent.reference.controller.applications.view_report_controller")
 */
class ViewReportController extends Controller
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
     * Latest application report
     *
     * @Route("/{applicationId}/report")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param $applicationId
     * @return Response
     */
    public function viewApplicationReportAction($applicationId)
    {
        try {
            /** @var Report $report */
            $report = $this->irisEntityManager->find(new Report(), array(
                'applicationId' => $applicationId,
            ));
        }
        catch(NotFoundException $ex) {
            // Case could not be found
            throw $this->createNotFoundException(sprintf('Application with applicationId "%s" could not be found', $applicationId));
        }
        
        return new Response($report, 200, array(
            'Content-Type' => $report->getContentType(),
        ));
    }
}
