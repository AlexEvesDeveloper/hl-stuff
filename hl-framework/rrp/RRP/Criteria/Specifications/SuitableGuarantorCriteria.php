<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class SuitableGuarantorCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class SuitableGuarantorCriteria extends AbstractCriteria
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        $recommendationStatus = $reference->getDecisionDetails()->getRecommendation()->getStatus();

        if (
            RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR != $recommendationStatus &&
            RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR_WITH_CONDITION != $recommendationStatus
        ) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Reference %s is not acceptable with a suitable guarantor',
                        $reference->getParent()->getReferenceNumber()
                    )
                )
                ->setIsSatisfied(false)
            ;


            return false;
        }

        return true;
    }
}