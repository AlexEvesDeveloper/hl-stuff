<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ProductIds;
use RRP\Common\Enumerations\CreditScoreCriteriaLimits;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class CreditScoreCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class CreditScoreCriteria extends AbstractCriteria
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        $creditScore = $reference->getDecisionDetails()->getCreditReference()->getScore();

        if (CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE > $creditScore) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Credit score criteria is not met. Reference %s credit score is %d',
                        $reference->getParent()->getReferenceNumber(),
                        $creditScore
                    )
                )
                ->setIsSatisfied(false)
            ;

            return false;
        }

        return true;
    }
}