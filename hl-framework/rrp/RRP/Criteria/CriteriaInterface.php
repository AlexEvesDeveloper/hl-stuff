<?php

namespace RRP\Criteria;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Interface CriteriaInterface
 *
 * @package RRP\Criteria
 * @author Alex Eves <alex.eves@barbon.com>
 */
interface CriteriaInterface
{
    /**
     * Determine if $reference satisfies this Criteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference);
}