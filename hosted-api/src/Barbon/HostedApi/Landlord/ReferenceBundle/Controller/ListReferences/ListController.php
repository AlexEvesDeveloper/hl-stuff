<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\ListReferences;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\PaymentStatusCodes;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Barbon\HostedApi\AppBundle\Form\Common\Lookup\ApplicationStatus;
use Barbon\HostedApi\AppBundle\Service\Reference\ReferenceRetriever;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * SearchController for Landlord Reference searches 
 *
 * @Route(service="barbon.hosted_api.landlord.reference.controller.list_references.list_controller")
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ListController extends Controller
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
     * @Route()
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // get references from IRIS
        $results = $this->referenceRetriever->getReferencesByStatus(null, true);

        // Now filter out all references that haven't had a successful payment
        $filteredResults = $this->referenceRetriever->filterByPaymentStatus($results, array(PaymentStatusCodes::SUCCESS));

        // Update the result with the filtered out results.
        $results->setRecords($filteredResults);
        $results->setTotalRecords(count($filteredResults));

        return array(
            'results' => $results
        );
    }
}