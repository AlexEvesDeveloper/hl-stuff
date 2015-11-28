<?php

namespace RRP\Criteria;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class Plus
 *
 * @package RRP\Criteria
 * @author Alex Eves <alex.eves@barbon.com>
 */
class Plus extends AbstractCriteria
{
    /**
     * @var AbstractCriteria
     */
    private $left;

    /**
     * @var AbstractCriteria
     */
    private $right;

    /**
     * Plus Constructor.
     *
     * @param CriteriaInterface $left
     * @param CriteriaInterface $right
     */
    public function __construct(CriteriaInterface $left, CriteriaInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Returns true if the left && right criteria evaluate to true
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    public function isSatisfiedBy(RentRecoveryPlusReference $reference)
    {
        $left = $this->left->isSatisfiedBy($reference);
        $right = $this->right->isSatisfiedBy($reference);

        return $left && $right;
    }
}