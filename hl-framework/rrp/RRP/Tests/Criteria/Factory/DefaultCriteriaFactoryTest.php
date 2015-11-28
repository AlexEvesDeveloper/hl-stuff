<?php

namespace RRP\Tests\Criteria\Factory;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\Factory\DefaultCriteriaFactory;

/**
 * Class DefaultCriteriaFactoryTest
 *
 * @package RRP\Tests\Criteria\Factory
 * @author Alex Eves <alex.eves@barbon.com>
 */
class DefaultCriteriaFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock_ReferencingApplication
     */
    protected $reference;

    /**
     * @var Mock_RrpGuarantorReferenceCreator
     */
    protected $guarantorCreatorStub;

    /**
     * @var Mock_RentRecoveryPlusReference
     */
    protected $rrpReferenceMock;

    /**
     * @var Mock_ReferencingDecisionDetails
     */
    protected $decisionDetailsMock;

    /**
     * @var Mock_Recommendation
     */
    protected $recommendationMock;

    /**
     * Pre test set up
     */
    public function setUp()
    {
        $this->reference = new ReferencingApplication();

        $this->guarantorCreatorStub = $this->getMockBuilder('RRP\Utility\RrpGuarantorReferenceCreator')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the chain: $reference->getDecisionDetails()->getRecommendation()
        $this->rrpReferenceMock = $this->getMock('RRP\Model\RentRecoveryPlusReference');

        $this->decisionDetailsMock = $this->getMockBuilder('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails')
            ->setMethods(array('getRecommendation'))
            ->getMock();

        $this->recommendationMock = $this->getMockBuilder('Barbondev\IRISSDK\Common\Model\Recommendation')
            ->setMethods(array('getStatus'))
            ->getMock();

        $this->rrpReferenceMock->expects($this->once())->method('getDecisionDetails')->willReturn($this->decisionDetailsMock);
        $this->decisionDetailsMock->expects($this->once())->method('getRecommendation')->willReturn($this->recommendationMock);
    }

    /**
     * @test
     */
    public function default_factory_with_acceptable_tenant_creates_a_no_guarantor_check_criteria_group()
    {
        $this->recommendationMock->expects($this->any())->method('getStatus')->willReturn(RecommendationStatuses::ACCEPTABLE);

        $factory = new DefaultCriteriaFactory($this->guarantorCreatorStub);
        $criteriaGroup = $factory->create($this->rrpReferenceMock);
        $this->assertInstanceOf('RRP\Criteria\SpecificationGroups\DefaultNoGuarantorCheckCriteriaGroup', $criteriaGroup);
    }

    /**
     * @test
     */
    public function default_factory_with_conditionally_acceptable_tenant_creates_a_no_guarantor_check_criteria_group()
    {
        $this->recommendationMock->expects($this->any())->method('getStatus')->willReturn(RecommendationStatuses::ACCEPTABLE_WITH_CONDITION);

        $factory = new DefaultCriteriaFactory($this->guarantorCreatorStub);
        $criteriaGroup = $factory->create($this->rrpReferenceMock);
        $this->assertInstanceOf('RRP\Criteria\SpecificationGroups\DefaultNoGuarantorCheckCriteriaGroup', $criteriaGroup);
    }

    /**
     * @test
     * @dataProvider getUnacceptableReferenceStatuses
     */
    public function default_factory_with_unacceptable_tenant_creates_a_guarantor_check_criteria_group($recommendationStatus)
    {
        $this->recommendationMock->expects($this->any())->method('getStatus')->willReturn($recommendationStatus);

        $factory = new DefaultCriteriaFactory($this->guarantorCreatorStub);
        $criteriaGroup = $factory->create($this->rrpReferenceMock);
        $this->assertInstanceOf('RRP\Criteria\SpecificationGroups\DefaultGuarantorCheckCriteriaGroup', $criteriaGroup);
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