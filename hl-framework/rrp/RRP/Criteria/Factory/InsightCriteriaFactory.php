<?php

namespace RRP\Criteria\Factory;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\SpecificationGroups\InsightCriteriaGroup;
use RRP\Criteria\Specifications\AdverseCreditCriteria;
use RRP\Criteria\Specifications\CreditScoreCriteria;
use RRP\Criteria\Specifications\EmploymentCriteria;
use RRP\Criteria\Specifications\RentShareCriteria;
use RRP\Criteria\Specifications\StatusCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class InsightCriteriaFactory
 *
 * @package RRP\Criteria\Factory
 * @author Alex Eves <alex.eves@barbon.com>
 */
class InsightCriteriaFactory extends AbstractCriteriaFactory
{
    /**
     * Create the relevant criteria group.
     *
     * @param RentRecoveryPlusReference $reference
     * @return InsightCriteriaGroup
     */
    protected function createCriteria(RentRecoveryPlusReference $reference)
    {
        return new InsightCriteriaGroup(
            new StatusCriteria(),
            new CreditScoreCriteria(),
            new AdverseCreditCriteria(),
            new EmploymentCriteria()
        );
    }
}