<?php

namespace RRP\Criteria\Factory;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class AbstractCriteriaFactory
 *
 * @package RRP\Criteria\Factory
 * @author Alex Eves <alex.eves@barbon.com>
 */
abstract class AbstractCriteriaFactory
{
    /**
     * Create the relevant criteria group.
     *
     * @return AbstractCriteria a new set of criteria
     */
    abstract protected function createCriteria(RentRecoveryPlusReference $reference);

    /**
     * Public facing wrapper for AbstractCriteriaFactory::createCriteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return AbstractCriteria
     */
    public function create(RentRecoveryPlusReference $reference)
    {
        return $this->createCriteria($reference);
    }
}