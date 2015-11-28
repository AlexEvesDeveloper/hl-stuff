<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\Common\Model\Recommendation;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\Specifications\GuarantorOutcomeCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class GuarantorOutcomeCriteriaTest
 *
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class GuarantorOutcomeCriteriaTest  extends \PHPUnit_Framework_TestCase
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
     * @var Recommendation
     */
    protected $recommendation;

    /**
     * @var RentRecoveryPlusReference
     */
    protected $guarantorRrpReference;

    /**
     * @var Mock_RrpGuarantorReferenceCreator
     */
    protected $guarantorCreatorMock;

    public function setUp()
    {
        $this->decisionDetails = new ReferencingDecisionDetails();
        $this->recommendation = new Recommendation();
        $this->guarantorRrpReference = new RentRecoveryPlusReference();

        $this->guarantorRrpReference->setParent(new ReferencingApplication());
        $this->guarantorRrpReference->setDecisionDetails($this->decisionDetails);
        $this->decisionDetails->setRecommendation($this->recommendation);

        $guarantorCreatorMock = $this->getMockBuilder('RRP\Utility\RrpGuarantorReferenceCreator')
            ->disableOriginalConstructor()
            ->getMock();

        $guarantorCreatorMock->expects($this->once())
            ->method('getGuarantor')
            ->willReturn($this->guarantorRrpReference);

        $this->criteria = new GuarantorOutcomeCriteria($guarantorCreatorMock);
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_guarantor_is_acceptable()
    {
        $this->recommendation->setStatus(RecommendationStatuses::ACCEPTABLE);

        $this->assertTrue($this->criteria->isSatisfiedBy(new RentRecoveryPlusReference()));
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_guarantor_is_acceptable_with_condition()
    {
        $this->recommendation->setStatus(RecommendationStatuses::ACCEPTABLE_WITH_CONDITION);

        $this->assertTrue($this->criteria->isSatisfiedBy(new RentRecoveryPlusReference()));
    }

    /**
     * @test
     * @dataProvider getUnacceptableReferenceStatuses
     */
    public function criteria_is_not_satisfied_when_guarantor_is_not_acceptable($recommendationStatus)
    {
        $this->recommendation->setStatus($recommendationStatus);

        $this->assertFalse($this->criteria->isSatisfiedBy(new RentRecoveryPlusReference()));
    }

    /**
     * Get Unacceptable reference statuses
     *
     * @return array
     */
    public function getUnacceptableReferenceStatuses()
    {
        return array(
            array(RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR),
            array(RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR_WITH_CONDITION),
            array(RecommendationStatuses::NOT_ACCEPTABLE),
            array(RecommendationStatuses::AWAITING_COMPLETION_OF_LEAD_TENANT),
            array(RecommendationStatuses::AWAITING_FURTHER_INFORMATION),
            array(RecommendationStatuses::ZERO_RENT_COTENANT_ACCEPTABLE),
            array(RecommendationStatuses::ZERO_RENT_COTENANT_NOT_ACCEPTABLE)
        );
    }
}