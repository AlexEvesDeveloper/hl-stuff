<?php

namespace RRP\Criteria\SpecificationGroups;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ProductIds;
use RRP\Criteria\AbstractCriteria;
use RRP\Criteria\CriteriaFactory;
use RRP\Criteria\Factory\AbstractCriteriaFactory;
use RRP\Criteria\Factory\DefaultCriteriaFactory;
use RRP\Criteria\Factory\InsightCriteriaFactory;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Utility\RrpGuarantorReferenceCreator;

/**
 * Class PolicyCriteria
 *
 * @package RRP\Criteria\SpecificationGroups
 * @author Alex Eves <alex.eves@barbon.com>
 */
class PolicyCriteria extends AbstractCriteria
{
    /**
     * @var AbstractCriteria
     */
    protected $criteriaStrategy;

    /**
     * @var AbstractCriteriaFactory
     */
    protected $factory;

    /**
     * @var RrpGuarantorReferenceCreator
     */
    protected $rrpGuarantorReferenceCreator;

    /**
     * ReferencingApplication constructor.
     *
     * @param RentRecoveryPlusReference $reference
     */
    public function __construct(RentRecoveryPlusReference $reference, RrpGuarantorReferenceCreator $rrpGuarantorReferenceCreator)
    {
        $this->rrpGuarantorReferenceCreator = $rrpGuarantorReferenceCreator;
        $this->criteriaStrategy = $this->createCriteriaStrategy($reference);
    }

    /**
     * Wrapper for isSatisfiedBy() on the $criteriaStrategy.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        return $this->criteriaStrategy->isSatisfiedBy($reference);
    }

    /**
     * Set $criteriaStrategy.
     *
     * @param AbstractCriteria $criteriaStrategy
     */
    public function setCriteriaStrategy(AbstractCriteria $criteriaStrategy)
    {
        $this->criteriaStrategy = $criteriaStrategy;
    }

    /**
     * Get $criteriaStrategy.
     *
     * @return AbstractCriteria
     */
    public function getCriteriaStrategy()
    {
        return $this->criteriaStrategy;
    }

    /**
     * Get $factory.
     *
     * @return AbstractCriteriaFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Wrapper for getNotSatisifiedText() on the $criteriaStrategy.
     *
     * @return string
     */
    public function getNotSatisfiedText()
    {
        return $this->criteriaStrategy->getNotSatisfiedText();
    }

    /**
     * Determine which strategy to use and create the appropriate criteria group through its factory.
     *
     * @param RentRecoveryPlusReference $reference
     * @return AbstractCriteria
     */
    protected function createCriteriaStrategy(RentRecoveryPlusReference $reference)
    {
        if ($reference->getParent()->getProductId() == ProductIds::INSIGHT) {
            $this->factory = new InsightCriteriaFactory();
        }
        else {
            $this->factory = new DefaultCriteriaFactory($this->rrpGuarantorReferenceCreator);
        }

        return $this->factory->create($reference);
    }
}