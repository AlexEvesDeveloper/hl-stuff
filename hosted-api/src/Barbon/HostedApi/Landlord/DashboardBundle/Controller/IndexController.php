<?php

namespace Barbon\HostedApi\Landlord\DashboardBundle\Controller;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\PaymentStatusCodes;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationSummaryCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Barbon\HostedApi\AppBundle\Form\Common\Lookup\ApplicationStatus;
use Barbon\HostedApi\AppBundle\Service\Reference\ReferenceRetriever;
use Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Model\DirectLandlord;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Landlord dashboard page
 *
 * @Route("/", service="barbon.hosted_api.landlord.dashboard.controller.index.index_controller")
 */
class IndexController extends Controller
{
    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * @var ReferenceRetriever
     */
    private $referenceRetriever;

    /**
     * @var ApplicationStatus
     */
    private $applicationStatusLookup;

    /**
     * @var TokenStorageInterface $tokenStorage
     */
    private $tokenStorage;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param ReferenceRetriever $referenceRetriever
     * @param ApplicationStatus $applicationStatusLookup
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        IrisEntityManager $irisEntityManager,
        ReferenceRetriever $referenceRetriever,
        ApplicationStatus $applicationStatusLookup,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->referenceRetriever = $referenceRetriever;
        $this->applicationStatusLookup = $applicationStatusLookup;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get ALL references from IRIS that have been paid for.
     *
     * @Route()
     * @Template()
     * @Method({"GET"})
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        // Firstly, get all references for this user, regardless of application status and payment status.
        $resultSet = $this->referenceRetriever->getReferencesByStatus(null, true);

        if ( ! $resultSet instanceof ReferencingApplicationSummaryCollection) {
            return array(
                'landlord' => $this->irisEntityManager->find(new DirectLandlord()),
                'results' => array(),
                'viewMore' => false
            );
        }

        // Now filter out all references that haven't had a successful payment
        $filteredResults = $this->referenceRetriever->filterByPaymentStatus($resultSet, array(PaymentStatusCodes::SUCCESS));

        // Update the result with the filtered out results.
        $resultSet->setRecords((array)$filteredResults);

        // Only show the 5 most recent results on the dashboard.
        // Break results into 5 per page...
        if (0 < count($resultSet->getRecords())) {
            $resultPages = array_chunk($resultSet->getRecords(), 5);
            // ...then only return the 1st page
            $resultSet->setRecords(reset($resultPages));

            // Do we have more than 1 page?
            $viewMore = (1 < count($resultPages));
        } else {
            // We filtered down to an empty result set.
            $viewMore = false;
            $resultSet = array();
        }

        return array(
            'landlord' => $this->irisEntityManager->find(new DirectLandlord()),
            'results' => $resultSet,
            'viewMore' => $viewMore
        );
    }
}
