<?php

namespace Barbon\HostedApi\Agent\ReferenceBundle\Controller\ListReferences;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Barbon\HostedApi\AppBundle\Form\Common\Lookup\ApplicationStatus;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationSummaryCollection;
use Barbon\HostedApi\AppBundle\Service\Reference\ReferenceRetriever;

/**
 * SearchController for Agent Reference searches 
 *
 * @Route(service="barbon.hosted_api.agent.reference.controller.list_references.list_controller")
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ListController extends Controller
{
    /**
     * @var ReferenceRetriever
     */
    private $referenceRetriever;

    /**
     * @var ApplicationStatus
     */
    private $applicationStatusLookup;

    /**
     * Constructor
     *
     * @param ApplicationStatus $applicationStatusLookup
     */
    public function __construct(ReferenceRetriever $referenceRetriever, ApplicationStatus $applicationStatusLookup)
    {
        $this->referenceRetriever = $referenceRetriever;
        $this->applicationStatusLookup = $applicationStatusLookup;
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
        $results = $this->referenceRetriever->getReferencesByStatus();
        // only process if results are found
        if ($results instanceof ReferencingApplicationSummaryCollection) {
            // grab the status labels from IRIS
            $statuses = $this->applicationStatusLookup->getLabels();

            // and apply to the appropriate label to each record
            $references = $results->getRecords();
            foreach ($references as &$reference) {
                $reference['statusText'] = $statuses[$reference['statusId']];
            }

            // update $results with the new $references
            $results->setRecords($references);
        }

        return array(
            'results' => $results
        );
    }
}
