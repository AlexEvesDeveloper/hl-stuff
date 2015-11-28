<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class AdverseCreditCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class AdverseCreditCriteria extends AbstractCriteria
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        if ($reference->getParent()->getHasCCJ()) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Adverse credit criteria is not met. Reference %s has adverse credit',
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