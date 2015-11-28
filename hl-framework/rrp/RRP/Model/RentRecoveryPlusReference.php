<?php

namespace RRP\Model;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;

/**
 * Class RentRecoveryPlusReference
 *
 * @package RRP\Model
 * @author Alex Eves <alex.eves@barbon.com>
 */
class RentRecoveryPlusReference
{
    /**
     * @var ReferencingApplication
     */
    private $parent;

    /**
     * @var ReferencingDecisionDetails
     */
    private $decisionDetails;

    /**
     * Get $parent
     *
     * @return ReferencingApplication
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set $parent
     *
     * @param ReferencingApplication $parent
     * @return RentRecoveryPlusReference
     */
    public function setParent(ReferencingApplication $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get $decisionDetails
     *
     * @return ReferencingDecisionDetails
     */
    public function getDecisionDetails()
    {
        return $this->decisionDetails;
    }

    /**
     * Set $decisionDetails
     *
     * @param ReferencingDecisionDetails $decisionDetails
     * @return RentRecoveryPlusReference
     */
    public function setDecisionDetails(ReferencingDecisionDetails $decisionDetails)
    {
        $this->decisionDetails = $decisionDetails;
        return $this;
    }
}