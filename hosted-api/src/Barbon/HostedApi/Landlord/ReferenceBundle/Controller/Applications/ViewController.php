<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\Applications;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\ApplicationType;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\Product;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Common\Model\Progress;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationCollection;
use Barbon\HostedApi\AppBundle\Traits\IrisModelRetrieverTrait;
use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/view", service="barbon.hosted_api.landlord.reference.controller.applications.view_controller")
 */
final class ViewController extends Controller
{
    use IrisModelRetrieverTrait;

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
            /** @var Progress $progress */
            $progress = $this->irisEntityManager->find(new Progress(), array(
                'applicationId' => $applicationId,
            ));
        }
        catch(NotFoundException $ex) {
            // Case could not be found
            throw $this->createNotFoundException(sprintf('Progress for applicationId "%s" could not be found', $applicationId));
        }

        $application = $this->getApplication($this->irisEntityManager, $applicationId);
        $case = $this->getCase($this->irisEntityManager, $application->getCaseId());

        $allowAddGuarantor = $this->isAllowedToAddGuarantor($application) ?: false;

        return array(
            'caseId' => $application->getCaseId(),
            'case' => $case,
            'application' => $application,
            'progress' => $progress,
            'allowAddGuarantor' => $allowAddGuarantor
        );
    }

    /**
     * @param ReferencingApplication $application
     * @return bool
     */
    private function isAllowedToAddGuarantor(ReferencingApplication $application)
    {
        // Guarantors can't have Guarantors
        if ($application->getApplicationType() == ApplicationType::GUARANTOR) {
            return false;
        }

        // Insight Tenants can't have Guarantors
        if ($application->getProduct()->getProductId() == Product::INSIGHT) {
            return false;
        }

        return true;
    }
}
