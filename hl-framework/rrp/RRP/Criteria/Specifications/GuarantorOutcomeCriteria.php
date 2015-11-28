<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Utility\RrpGuarantorReferenceCreator;

/**
 * Class GuarantorOutcomeCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class GuarantorOutcomeCriteria extends AbstractCriteria
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
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        $reference = $this->rrpGuarantorReferenceCreator->getGuarantor($reference);
        $recommendationStatus = $reference->getDecisionDetails()->getRecommendation()->getStatus();

        if (
            RecommendationStatuses::ACCEPTABLE != $recommendationStatus &&
            RecommendationStatuses::ACCEPTABLE_WITH_CONDITION != $recommendationStatus
        ) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Reference %s has the following outcome: %s',
                        $reference->getParent()->getReferenceNumber(),
                        $recommendationStatus
                    )
                )
                ->setIsSatisfied(false)
            ;

            return false;
        }

        return true;
    }
}