<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\Cases;

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
 * @Route("/view", service="barbon.hosted_api.landlord.reference.controller.cases.view_controller")
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
     * Case overview
     *
     * @Route("/{caseId}")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param $caseId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($caseId)
    {
        try {
            $case = $this->irisEntityManager->find(new ReferencingCase(), array(
                'caseId' => $caseId
            ));

            $applications = $this->irisEntityManager->find(new ReferencingApplicationCollection(), array(
                'caseId' => $caseId
            ));
        }
        catch(NotFoundException $ex) {
            // Case could not be found
            throw $this->createNotFoundException(sprintf('Case with caseId "%s" could not be found', $caseId));
        }

        return array(
            'caseId' => $caseId,
            'case' => $case,
            'applications' => $applications
        );
    }
}
