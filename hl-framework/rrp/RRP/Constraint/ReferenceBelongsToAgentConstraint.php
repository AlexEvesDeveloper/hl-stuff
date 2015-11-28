<?php

namespace RRP\Constraint;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResult;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use Iris\IndividualApplication\Search\IndividualApplicationSearch;
use RRP\Utility\SessionReferenceHolder;

/**
 * Class ReferenceBelongsToAgentConstraint
 *
 * @package RRP\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceBelongsToAgentConstraint implements ConstraintInterface
{
    /**
     * @var ClientRegistry
     */
    protected $clientRegistry;

    /**
     * @var IndividualApplicationSearch
     */
    protected $irisReferenceSearchClient;

    /**
     * @var SearchIndividualApplicationsCriteria
     */
    protected $searchCriteria;

    /**
     * @var SessionReferenceHolder
     */
    protected $sessionHolder;

    /**
     * ReferenceBelongsToAgentConstraint constructor
     *
     * @param ClientRegistry $clientRegistry
     * @param IndividualApplicationSearch $irisReferenceSearchClient
     * @param SearchIndividualApplicationsCriteria $searchCriteria
     * @param SessionReferenceHolder $sessionHolder
     */
    public function __construct(
        ClientRegistry $clientRegistry,
        IndividualApplicationSearch $irisReferenceSearchClient,
        SearchIndividualApplicationsCriteria $searchCriteria,
        SessionReferenceHolder $sessionHolder
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->irisReferenceSearchClient = $irisReferenceSearchClient;
        $this->searchCriteria = $searchCriteria;
        $this->sessionHolder = $sessionHolder;
    }

    /**
     * {@inheritdoc}
     */
    public function verify($referenceNumber, $data = array())
    {
        if ( ! array_key_exists('current_asn', $data)) {
            throw new \LogicException('current_asn key does not exist in $data array');
        }

        // Query IRIS to find a Reference of the given value for this Agent.
        // If IRIS did not return a result, apply an invalid reference number constraint.
        $this->searchCriteria->setReferenceNumber($referenceNumber);
        $result = $this->irisReferenceSearchClient->search($data['current_asn'], $this->searchCriteria, 0, 1);
        if (0 == $result->getTotalRecords()) {
            return false;
        }

        // Believe it or not, IRIS may have returned the wrong result.
        // If the user entered 12, meaning to enter 123, IRIS will return all 12* references.
        // Therefore, double check the reference number from IRIS against the one we searched for.
        $result = current($result->getRecords());
        if ($result->getReferenceNumber() != $referenceNumber) {
            return false;
        }

        // Convert the result into a ReferencingApplication model as it contains data that we will want in the session.
        $referencingApplication = $this->getReferencingApplication($result);

        // Put the reference into the session for other subscribers, to prevent duplicate lookups.
        $this->sessionHolder->putReferenceInSession($referencingApplication, $data['current_asn']);

        return true;

    }

    /**
     * Get a ReferencingApplication object from IRIS.
     *
     * @param ReferencingApplicationFindResult $result
     * @return ReferencingApplication
     */
    private function getReferencingApplication(ReferencingApplicationFindResult $result)
    {
        return $this->clientRegistry->getAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $result->getReferencingApplicationUuId()
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorText()
    {
        return 'We are unable to find this reference. Please ensure you have entered a valid reference number.';
    }
}