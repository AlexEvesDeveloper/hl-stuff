<?php

namespace Barbon\HostedApi\AppBundle\Service\Reference;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\ApplicationStatusCodes;
use Barbon\HostedApi\AppBundle\Form\Common\Model\PaymentStatus;
use Barbon\IrisRestClient\DataTransformer\Deserialiser\JsonDeserialiser\Exception\JsonInvalidException;
use Barbon\IrisRestClient\EntityManager\DefaultIrisEntityManager;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationSummaryCollection;

/**
 * Manages the retrieval of references from IRIS
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceRetriever
{
    /**
     * @var DefaultIrisEntityManager
     */
    private $irisEntityManager;


    /**
     * Constructor
     *
     * @param DefaultIrisEntityManager $irisEntityManager
     * @internal param ApplicationStatus $applicationStatusLookup
     */
    public function __construct(DefaultIrisEntityManager $irisEntityManager)
    {
        $this->irisEntityManager = $irisEntityManager;
    }

    /**
     * Get all references that match the given Reference Status
     *
     * @param string $status
     * @param bool $attachCaseIds
     *
     * @return array
     */
    public function getReferencesByStatus($status = null, $attachCaseIds = false)
    {
        $results = array();    

        try {
            $results = $this->irisEntityManager->find(new ReferencingApplicationSummaryCollection(), array(
                'applicationStatus' => $this->getApplicationStatusFilter(strtoupper($status))
            ));

            // If requested, attach the caseIds to each application record found
            // Note: this is optinal because currently doing this for Agents can cause timeouts
            if ($attachCaseIds && $results instanceof ReferencingApplicationSummaryCollection) {
                $applications = $results->getRecords();
                foreach ($applications as &$application) {
                    $model = $this->irisEntityManager->find(new ReferencingApplication(), array(
                        'applicationId' => $application['applicationUuid']
                    ));

                    $application['caseId'] = $model->getCaseId();
                }

                // update $results with the new $references
                $results->setRecords($applications);
            }
        }
        catch (JsonInvalidException $ex) {
            // returned empty results, but it's OK, Twig will handle it gracefully
        }

        return $results;
    }

    /**
     * @param ReferencingApplicationSummaryCollection|array $resultSet
     * @param array $statusFilters
     * @return \Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationSummary[]
     */
    public function filterByPaymentStatus(ReferencingApplicationSummaryCollection $resultSet, array $statusFilters)
    {
        $records = $resultSet->getRecords();

        foreach ($records as $key => $result) {
            $paymentStatus = $this->irisEntityManager->find(new PaymentStatus(), array(
                'applicationId' => $result['applicationUuid']
            ));

            // If the status of the current application isn't in the given array of allowable statuses, remove it from the list.
            if ( ! in_array($paymentStatus->getPaymentStatus(), $statusFilters)) {
                unset($records[$key]);
            }
        }

        return $records;
    }

    /**
     * Build a query string for the applicationStatus field
     *
     * @param string $status
     *
     * @return string
     */
    protected function getApplicationStatusFilter($status = null)
    {
        switch ($status) {
            case 'ACTIVE':
                return sprintf('%d, %d, %d, %d', 
                    ApplicationStatusCodes::INCOMPLETE,
                    ApplicationStatusCodes::IN_PROGRESS,
                    ApplicationStatusCodes::AWAITING_APPLICATION_DETAILS,
                    ApplicationStatusCodes::AWAITING_AGENT_REVIEW
                );
            case 'COMPLETE':
                return sprintf('%d', ApplicationStatusCodes::COMPLETE);
            case 'CANCELLED':
                return sprintf('%d', ApplicationStatusCodes::CANCELLED);
            case 'DECLINED':
                return sprintf('%d', ApplicationStatusCodes::DECLINED);
            default:
                // default to applying all filters
                return sprintf('%d, %d, %d, %d, %d, %d, %d', 
                    ApplicationStatusCodes::INCOMPLETE,
                    ApplicationStatusCodes::IN_PROGRESS,
                    ApplicationStatusCodes::AWAITING_APPLICATION_DETAILS,
                    ApplicationStatusCodes::AWAITING_AGENT_REVIEW,
                    ApplicationStatusCodes::COMPLETE,
                    ApplicationStatusCodes::CANCELLED,
                    ApplicationStatusCodes::DECLINED
                );            
        }
    }
}
