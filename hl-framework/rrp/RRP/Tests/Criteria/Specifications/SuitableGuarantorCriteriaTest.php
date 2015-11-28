<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\Common\Model\Recommendation;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;
use Iris\Common\Enumerations\RecommendationStatuses;
use RRP\Criteria\Specifications\SuitableGuarantorCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class SuitableGuarantorCriteriaTest
 * 
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class SuitableGuarantorCriteriaTest extends \PHPUnit_Framework_TestCase
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
    protected $rrpReference;

    public function setUp()
    {
        $this->criteria = new SuitableGuarantorCriteria();

        $this->reference = new ReferencingApplication();
        $this->decisionDetails = new ReferencingDecisionDetails();
        $this->recommendation = new Recommendation();
        $this->rrpReference = new RentRecoveryPlusReference();

        $this->rrpReference->setParent($this->reference);
        $this->rrpReference->setDecisionDetails($this->decisionDetails);
        $this->decisionDetails->setRecommendation($this->recommendation);
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_reference_is_acceptable_with_suitable_guarantor()
    {
        $this->recommendation->setStatus(RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR);

        $this->assertTrue($this->criteria->isSatisfiedBy($this->rrpReference));
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_reference_is_acceptable_with_suitable_guarantor_with_condition()
    {
        $this->recommendation->setStatus(RecommendationStatuses::ACCEPTABLE_WITH_GUARANTOR_WITH_CONDITION);

        $this->assertTrue($this->criteria->isSatisfiedBy($this->rrpReference));
    }

    /**
     * @test
     * @dataProvider getUnsuitableRecommendationStatuses
     */
    public function criteria_is_not_satisfied_when_reference_is_not_acceptable_with_suitable_guarantor(RentRecoveryPlusReference $reference)
    {
        $this->assertFalse($this->criteria->isSatisfiedBy($reference));
    }

    /**
     * @return array
     */
    public function getUnsuitableRecommendationStatuses()
    {
        $recommendationStatuses = array(
            'acceptable' => RecommendationStatuses::ACCEPTABLE,
            'acceptable_with_condition' => RecommendationStatuses::ACCEPTABLE_WITH_CONDITION,
            'not_acceptable' => RecommendationStatuses::NOT_ACCEPTABLE,
            'awaiting_further_information' => RecommendationStatuses::AWAITING_FURTHER_INFORMATION,
            'awaiting_completion_of_lead_tenant' => RecommendationStatuses::AWAITING_COMPLETION_OF_LEAD_TENANT,
            'zero_rent_cotenant_not_acceptable' => RecommendationStatuses::ZERO_RENT_COTENANT_NOT_ACCEPTABLE,
            'zero_rent_cotenant_acceptable' => RecommendationStatuses::ZERO_RENT_COTENANT_ACCEPTABLE
        );

        $references = array();
        foreach ($recommendationStatuses as $key => $recommendationStatus) {
            $recommendation = new Recommendation();
            $recommendation->setStatus($recommendationStatus);

            $decisionDetails = new ReferencingDecisionDetails();
            $decisionDetails->setRecommendation($recommendation);

            $reference = new ReferencingApplication();
            $rrpReference = new RentRecoveryPlusReference();
            $rrpReference->setDecisionDetails($decisionDetails);
            $rrpReference->setParent($reference);

            $references[$key] = $rrpReference;
        }

        return array(
            array($references['acceptable']),
            array($references['acceptable_with_condition']),
            array($references['not_acceptable']),
            array($references['awaiting_further_information']),
            array($references['awaiting_completion_of_lead_tenant']),
            array($references['zero_rent_cotenant_not_acceptable']),
            array($references['zero_rent_cotenant_acceptable']),
        );
    }
}