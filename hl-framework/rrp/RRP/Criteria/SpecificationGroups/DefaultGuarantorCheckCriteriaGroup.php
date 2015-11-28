<?php

namespace RRP\Criteria\SpecificationGroups;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class DefaultGuarantorCheckCriteriaGroup
 *
 * @package RRP\Criteria\SpecificationGroups
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DefaultGuarantorCheckCriteriaGroup extends AbstractCriteria
{
    /**
     * @var AbstractCriteria
     */
    protected $statusCriteria;

    /**
     * @var AbstractCriteria
     */
    protected $suitableGuarantorCriteria;

    /**
     * @var AbstractCriteria
     */
    protected $guarantorOutcomeCriteria;

    /**
     * @var array
     */
    protected $criterias;

    /**
     * InsightGroupCriteria constructor.
     *
     * @param AbstractCriteria $statusCriteria
     * @param AbstractCriteria $suitableGuarantorCriteria
     * @param AbstractCriteria $guarantorOutcomeCriteria
     */
    public function __construct(
        AbstractCriteria $statusCriteria,
        AbstractCriteria $suitableGuarantorCriteria,
        AbstractCriteria $guarantorOutcomeCriteria
    ) {
        $this->statusCriteria = $statusCriteria;
        $this->suitableGuarantorCriteria = $suitableGuarantorCriteria;
        $this->guarantorOutcomeCriteria = $guarantorOutcomeCriteria;

        $this->addCriteria($statusCriteria);
        $this->addCriteria($suitableGuarantorCriteria);
        $this->addCriteria($guarantorOutcomeCriteria);
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
            ->plus($this->suitableGuarantorCriteria)
            ->plus($this->guarantorOutcomeCriteria)
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