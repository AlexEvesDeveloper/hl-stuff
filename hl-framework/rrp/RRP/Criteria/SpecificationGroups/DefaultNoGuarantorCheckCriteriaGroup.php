<?php

namespace RRP\Criteria\SpecificationGroups;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class DefaultNoGuarantorCheckCriteriaGroup
 *
 * @package RRP\Criteria\SpecificationGroups
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DefaultNoGuarantorCheckCriteriaGroup extends AbstractCriteria
{
    /**
     * @var AbstractCriteria
     */
    protected $statusCriteria;

    /**
     * @var array
     */
    protected $criterias;

    /**
     * InsightGroupCriteria constructor.
     *
     * @param AbstractCriteria $statusCriteria
     */
    public function __construct(AbstractCriteria $statusCriteria)
    {
        $this->statusCriteria = $statusCriteria;

        $this->addCriteria($statusCriteria);
    }

    /**
     * Chains all criteria objects together and returns true if all criteria return true.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        return $this->statusCriteria
            ->isSatisfiedBy($reference);
    }

    /**
     * Get notSatisfiedText.
     *
     * @return array
     */
    public function getNotSatisfiedText()
    {
        $reasons = array();

        foreach ($this->criterias as $criteria) {
            if ( ! $criteria->isSatisfied()) {
                $reasons[] = $criteria->getNotSatisfiedText();
            }
        }

        return $reasons;
    }

    /**
     * Add an AbstractCriteria.
     *
     * @param AbstractCriteria $criteria
     */
    private function addCriteria(AbstractCriteria $criteria)
    {
        $this->criterias[] = $criteria;
    }
}