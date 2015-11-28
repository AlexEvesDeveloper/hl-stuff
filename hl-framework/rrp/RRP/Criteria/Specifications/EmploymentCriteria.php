<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\EmploymentStatuses;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class EmploymentCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class EmploymentCriteria extends AbstractCriteria
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        if (EmploymentStatuses::EMPLOYED != $reference->getParent()->getEmploymentStatus()) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Employment criteria is not met. Reference %s employment status is %d',
                        $reference->getParent()->getReferenceNumber(),
                        $reference->getParent()->getEmploymentStatus()
                    )
                )
                ->setIsSatisfied(false)
            ;

            return false;
        }

        return true;
    }
}