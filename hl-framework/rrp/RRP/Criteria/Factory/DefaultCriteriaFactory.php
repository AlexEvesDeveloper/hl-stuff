<?php

namespace RRP\Criteria\Factory;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\SpecificationGroups\DefaultGuarantorCheckCriteriaGroup;
use RRP\Criteria\SpecificationGroups\DefaultNoGuarantorCheckCriteriaGroup;
use RRP\Criteria\Specifications\GuarantorOutcomeCriteria;
use RRP\Criteria\Specifications\StatusCriteria;
use RRP\Criteria\Specifications\SuitableGuarantorCriteria;
use RRP\Criteria\Specifications\TenantOutcomeCriteria;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Utility\RrpGuarantorReferenceCreator;

/**
 * Class DefaultCriteriaFactory
 *
 * @package RRP\Criteria\Factory
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DefaultCriteriaFactory extends AbstractCriteriaFactory
{
    /**
     * @var RrpGuarantorReferenceCreator
     */
    protected $rrpGuarantorReferenceCreator;

    /**
     * ReferencingApplication constructor.
     *
     * @param RrpGuarantorReferenceCreator $rrpGuarantorReferenceCreator
     */
    public function __construct(RrpGuarantorReferenceCreator $rrpGuarantorReferenceCreator)
    {
        $this->rrpGuarantorReferenceCreator = $rrpGuarantorReferenceCreator;
    }

    /**
     * Create the relevant criteria group.
     *
     * @param RentRecoveryPlusReference $reference
     * @return DefaultGuarantorCheckCriteriaGroup|DefaultNoGuarantorCheckCriteriaGroup
     */
    protected function createCriteria(RentRecoveryPlusReference $reference)
    {
        // Only need guarantor checks if tenant is not acceptable
        $decisionOutcome = $reference->getDecisionDetails()->getRecommendation()->getStatus();
        $needsGuarantorCheck = RecommendationStatuses::ACCEPTABLE != $decisionOutcome &&
            RecommendationStatuses::ACCEPTABLE_WITH_CONDITION != $decisionOutcome;

        if ($needsGuarantorCheck) {
            return new DefaultGuarantorCheckCriteriaGroup(
                new StatusCriteria(),
                new SuitableGuarantorCriteria(),
                new GuarantorOutcomeCriteria($this->rrpGuarantorReferenceCreator)
            );
        }
        else {
            return new DefaultNoGuarantorCheckCriteriaGroup(
                new StatusCriteria()
            );
        }
    }
}