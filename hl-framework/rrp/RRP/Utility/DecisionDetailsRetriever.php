<?php

namespace RRP\Utility;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;


/**
 * Class DecisionDetailsRetriever
 *
 * Retrieves a DecisionDetails object from IRIS via the IRIS PHP SDK.
 *
 * @package RRP\Model\Utility
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DecisionDetailsRetriever
{
    /**
     * @var ClientRegistry
     */
    protected $clientRegistry;

    /**
     * DecisionDetailsRetriever constructor.
     *
     * @param ClientRegistry $clientRegistry
     */
    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    /**
     * Get DecisionDetails object from IRIS.
     *
     * @param ReferencingApplication $reference
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails
     */
    public function getDecisionDetails(ReferencingApplication $reference)
    {
        return $this->clientRegistry->getAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingDecisionDetails(array(
                'referencingApplicationUuId' => $reference->getReferencingApplicationUuId()
            ));
    }
}