<?php

namespace RRP\Utility;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Model\RentRecoveryPlusReference;


/**
 * Class RrpGuarantorReferenceCreator
 *
 * @package RRP\Utility
 * @author Alex Eves <alex.eves@barbon.com>
 */
class RrpGuarantorReferenceCreator
{
    /**
     * @var ClientRegistry
     */
    protected $clientRegistry;

    /**
     * @var DecisionDetailsRetriever
     */
    protected $decisionDetailsRetriever;

    /**
     * @var RentRecoveryPlusReference
     */
    protected $rrpGuarantor;

    /**
     * RrpGuarantorReferenceCreator constructor.
     *
     * @param ClientRegistry $clientRegistry
     * @param DecisionDetailsRetriever $decisionDetailsRetriever
     */
    public function __construct(ClientRegistry $clientRegistry, DecisionDetailsRetriever $decisionDetailsRetriever)
    {
        $this->clientRegistry = $clientRegistry;
        $this->decisionDetailsRetriever = $decisionDetailsRetriever;
    }

    /**
     * Get the guarantor from IRIS, unless we already have in this request, in which case get that one.
     *
     * @param RentRecoveryPlusReference $tenant
     * @return RentRecoveryPlusReference
     */
    public function getGuarantor(RentRecoveryPlusReference $tenant)
    {
        if (null === $this->rrpGuarantor) {
            $this->rrpGuarantor = $this->createGuarantor($tenant);
        }

        return $this->rrpGuarantor;
    }

    /**
     * Get a guarantor ReferencingApplication from IRIS, attach it to a RentRecoveryPlusReference and return it.
     *
     * @param RentRecoveryPlusReference $tenant
     * @return RentRecoveryPlusReference
     */
    private function createGuarantor(RentRecoveryPlusReference $tenant)
    {
        $guarantorCollection = $this->clientRegistry->getAgentContext()
            ->getReferencingApplicationClient()
            ->getReferencingApplicationGuarantors(array(
                'referencingApplicationUuId' => $tenant->getParent()->getReferencingApplicationUuid()
            ));

        // todo: Grab the first one for now - business will likely want to consider all Guarantors however.
        $guarantor = current($guarantorCollection->getAll());
        $this->rrpGuarantor = new RentRecoveryPlusReference();
        $this->rrpGuarantor
            ->setParent($guarantor)
            ->setDecisionDetails($this->decisionDetailsRetriever->getDecisionDetails($guarantor));

        return $this->rrpGuarantor;
    }
}