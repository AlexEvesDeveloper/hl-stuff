<?php

namespace RRP\Tests\Criteria;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ApplicationStatuses;
use Iris\Common\Enumerations\EmploymentStatuses;
use Iris\Common\Enumerations\ProductIds;
use RRP\Common\Enumerations\CreditScoreCriteriaLimits;
use RRP\Criteria\Specifications\AdverseCreditCriteria;
use RRP\Criteria\Specifications\CreditScoreCriteria;
use RRP\Criteria\Specifications\EmploymentCriteria;
use RRP\Criteria\SpecificationGroups\InsightCriteriaGroup;
use RRP\Criteria\Specifications\StatusCriteria;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Rate\RateDecorators\RentRecoveryPlus;

/**
 * Class InsightCriteriaGroupTest
 *
 * @package RRP\Tests\Criteria
 * @author Alex Eves <alex.eves@barbon.com>
 */
class InsightCriteriaGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InsightGroupCriteria
     */
    protected $insightCriteria;

    /**
     * @var ReferencingApplication
     */
    protected $reference;

    /**
     * @var Mock_RentRecoveryPlusReference
     */
    protected $mockRrpReference;

    /**
     * @var Mock_ReferencingDecisionDetails
     */
    protected $mockDecisionDetails;

    /**
     * @var Mock_CreditReference
     */
    protected $mockCreditReference;

    /**
     * Pre test set up.
     */
    public function setUp()
    {
        $this->reference = new ReferencingApplication();

        // Mock the following: $rrpReference->getDecisionDetails()->getCreditReference()->getScore()
        // Create the mocks, declaring the methods they will call
        $this->mockRrpReference = $this->getMockBuilder('RRP\Model\RentRecoveryPlusReference')
            ->setMethods(array('getParent', 'getDecisionDetails'))
            ->getMock();

        $this->mockDecisionDetails = $this->getMockBuilder('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails')
            ->setMethods(array('getCreditReference'))
            ->getMock();

        $this->mockCreditReference = $this->getMockBuilder('Barbondev\IRISSDK\Common\Model\CreditReference')
            ->setMethods(array('getScore'))
            ->getMock();

        // Declare the results of their expected methods
        $this->mockRrpReference->expects($this->any())
            ->method('getParent')
            ->willReturn($this->reference);

        $this->mockRrpReference->expects($this->any())
            ->method('getDecisionDetails')
            ->willReturn($this->mockDecisionDetails);

        $this->mockDecisionDetails->expects($this->any())
            ->method('getCreditReference')
            ->willReturn($this->mockCreditReference);

        $this->insightCriteria = new InsightCriteriaGroup(
            new StatusCriteria(),
            new CreditScoreCriteria(),
            new AdverseCreditCriteria(),
            new EmploymentCriteria()
        );
    }

    /**
     * @test
     */
    public function an_acceptable_reference_returns_true()
    {
        $this->reference
            ->setProductId(ProductIds::INSIGHT)
            ->setStatus(ApplicationStatuses::COMPLETE)
            ->setHasCCJ(false)
            ->setEmploymentStatus(EmploymentStatuses::EMPLOYED);

        $this->mockCreditReference->expects($this->any())
            ->method('getScore')
            ->willReturn(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE);

        $this->assertTrue($this->insightCriteria->isSatisfiedBy($this->mockRrpReference));
    }

    /**
     * @test
     */
    public function an_acceptable_reference_logs_no_failure_reasons()
    {
        $this->reference
            ->setProductId(ProductIds::INSIGHT)
            ->setStatus(ApplicationStatuses::COMPLETE)
            ->setHasCCJ(false)
            ->setEmploymentStatus(EmploymentStatuses::EMPLOYED);

        $this->mockCreditReference->expects($this->any())
            ->method('getScore')
            ->willReturn(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE);

        $this->insightCriteria->isSatisfiedBy($this->mockRrpReference);

        // No failing criteria are logged
        $this->assertEquals(0, count($this->insightCriteria->getNotSatisfiedText()));
    }

    /**
     * @test
     */
    public function an_unacceptable_reference_returns_false()
    {
        $this->reference
            ->setProductId(ProductIds::INSIGHT)
            ->setStatus(ApplicationStatuses::INCOMPLETE)
            ->setHasCCJ(true)
            ->setEmploymentStatus(EmploymentStatuses::UNEMPLOYED);

        $this->mockCreditReference->expects($this->any())
            ->method('getScore')
            ->willReturn(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE - 1);

        // The reference has 4 reasons to fail acceptance.
        $this->assertFalse($this->insightCriteria->isSatisfiedBy($this->mockRrpReference));
    }

    /**
     * @test
     */
    public function an_unacceptable_reference_logs_its_failure_reasons()
    {
        $this->reference
            ->setProductId(ProductIds::INSIGHT)
            ->setStatus(ApplicationStatuses::INCOMPLETE)
            ->setHasCCJ(true)
            ->setEmploymentStatus(EmploymentStatuses::UNEMPLOYED);

        $this->mockCreditReference->expects($this->any())
            ->method('getScore')
            ->willReturn(CreditScoreCriteriaLimits::MINIMUM_INSIGHT_CREDIT_SCORE - 1);

        // The reference has 4 reasons to fail acceptance.
        $this->insightCriteria->isSatisfiedBy($this->mockRrpReference);

        // 4 failing criteria tests should result in 4 logged failure messages.
        $this->assertEquals(4, count($this->insightCriteria->getNotSatisfiedText()));
    }
}