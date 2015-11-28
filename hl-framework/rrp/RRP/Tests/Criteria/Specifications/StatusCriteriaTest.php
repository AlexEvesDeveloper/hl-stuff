<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ApplicationStatuses;
use RRP\Criteria\CriteriaInterface;
use RRP\Criteria\Specifications\StatusCriteria;
use RRP\Model\RentRecoveryPlusReference;


/**
 * Class StatusCriteriaTest
 *
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class StatusCriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CriteriaInterface
     */
    protected $criteria;

    /**
     * Pre test set up.
     */
    public function setUp()
    {
        $this->criteria = new StatusCriteria();
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_reference_is_complete()
    {
        $reference = new ReferencingApplication();
        $reference->setStatus(ApplicationStatuses::COMPLETE);

        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        $this->assertTrue($this->criteria->isSatisfiedBy($rrpReference));
    }

    /**
     * @test
     * @dataProvider getIncompleteReferences
     */
    public function criteria_is_not_satisfied_when_reference_is_not_complete(RentRecoveryPlusReference $referenceMock)
    {
        $this->assertFalse($this->criteria->isSatisfiedBy($referenceMock));
    }

    /**
     * @return array
     */
    public function getIncompleteReferences()
    {
        $incompleteStatuses = array(
            'incomplete' => ApplicationStatuses::INCOMPLETE,
            'in_progress' => ApplicationStatuses::IN_PROGRESS,
            'awaiting_application_details' => ApplicationStatuses::AWAITING_APPLICATION_DETAILS,
            'cancelled' => ApplicationStatuses::CANCELLED,
            'declined' => ApplicationStatuses::DECLINED,
            'awaiting_agent_review' => ApplicationStatuses::AWAITING_AGENT_REVIEW
        );

        $references = array();
        foreach ($incompleteStatuses as $key => $incompleteStatus) {
            $reference = new ReferencingApplication();
            $reference->setStatus($incompleteStatus);

            $rrpReference = new RentRecoveryPlusReference();
            $rrpReference->setParent($reference);

            $references[$key] = $rrpReference;
        }

        return array(
            array($references['incomplete']),
            array($references['in_progress']),
            array($references['awaiting_application_details']),
            array($references['cancelled']),
            array($references['declined']),
            array($references['awaiting_agent_review']),
        );
    }
}