<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\Common\Model\CreditReference;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;
use Iris\Common\Enumerations\ProductIds;
use RRP\Common\Enumerations\CreditScoreCriteriaLimits;
use RRP\Criteria\CriteriaInterface;
use RRP\Criteria\Specifications\CreditScoreCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class CreditScoreCriteriaTest
 *
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class CreditScoreCriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CriteriaInterface
     */
    protected $criteria;

    /**
     * @var ReferencingApplication
     */
    protected $reference;

    /**
     * @var ReferencingDecisionDetails
     */
    protected $decisionDetails;

    /**
     * @var CreditReference
     */
    protected $creditReference;

    /**
     * @var RentRecoveryPlusReference
     */
    protected $rrpReference;

    /**
     * Pre test set up.
     */
    public function setUp()
    {
        $this->criteria = new CreditScoreCriteria();

        $this->reference = new ReferencingApplication();
        $this->decisionDetails = new ReferencingDecisionDetails();
        $this->creditReference = new CreditReference();
        $this->rrpReference = new RentRecoveryPlusReference();

        $this->rrpReference->setParent($this->reference);
        $this->rrpReference->setDecisionDetails($this->decisionDetails);
        $this->decisionDetails->setCreditReference($this->creditReference);
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_credit_score_is_satisfactory_for_insight()
    {
        $this->reference->setProductId(ProductIds::INSIGHT);
        $this->creditReference->setScore(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE);

        $this->assertTrue($this->criteria->isSatisfiedBy($this->rrpReference));
    }

    /**
     * @test
     */
    public function criteria_is_not_satisfied_when_credit_score_is_unsatisfactory_for_insight()
    {
        $this->reference->setProductId(ProductIds::INSIGHT);
        $this->creditReference->setScore(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE - 1);

        $this->assertFalse($this->criteria->isSatisfiedBy($this->rrpReference));
    }
}