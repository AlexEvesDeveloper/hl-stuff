<?php

namespace RRP\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ApplicationStatuses;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class StatusCriteria
 *
 * @package RRP\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class StatusCriteria extends AbstractCriteria
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        if (ApplicationStatuses::COMPLETE != $reference->getParent()->getStatus()) {
            $this
                ->setNotSatisfiedText(
                    sprintf(
                        'Status criteria is not met. Reference %s status is %d',
                        $reference->getParent()->getReferenceNumber(),
                        $reference->getParent()->getStatus()
                    )
                )
                ->setIsSatisfied(false)
            ;

            return false;
        }

        return true;
    }
}