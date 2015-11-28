<?php

namespace RRP\Criteria\SpecificationGroups;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\AbstractCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class InsightCriteriaGroup
 *
 * @package RRP\Criteria\SpecificationGroups
 * @author Alex Eves <alex.eves@barbon.com>
 */
class InsightCriteriaGroup extends AbstractCriteria
{
    /**
     * @var AbstractCriteria
     */
    protected $statusCriteria;

    /**
     * @var AbstractCriteria
     */
    protected $creditScoreCriteria;

    /**
     * @var AbstractCriteria
     */
    protected $adverseCreditCriteria;

    /**
     * @var AbstractCriteria
     */
    protected $employmentCriteria;

    /**
     * @var array
     */
    protected $criterias;

    /**
     * InsightGroupCriteria constructor.
     *
     * @param AbstractCriteria $statusCriteria
     * @param AbstractCriteria $creditScoreCriteria
     * @param AbstractCriteria $adverseCreditCriteria
     * @param AbstractCriteria $employmentCriteria
     */
    public function __construct(
        AbstractCriteria $statusCriteria,
        AbstractCriteria $creditScoreCriteria,
        AbstractCriteria $adverseCreditCriteria,
        AbstractCriteria $employmentCriteria
    ) {
        $this->statusCriteria = $statusCriteria;
        $this->creditScoreCriteria = $creditScoreCriteria;
        $this->adverseCreditCriteria = $adverseCreditCriteria;
        $this->employmentCriteria = $employmentCriteria;

        $this->addCriteria($statusCriteria);
        $this->addCriteria($creditScoreCriteria);
        $this->addCriteria($adverseCreditCriteria);
        $this->addCriteria($employmentCriteria);
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
            ->plus($this->creditScoreCriteria)
            ->plus($this->adverseCreditCriteria)
            ->plus($this->employmentCriteria)
            ->isSatisfiedBy($reference);
    }

    /**
     * Composite: get $notSatisfiedText for all failing Criteria in this group.
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