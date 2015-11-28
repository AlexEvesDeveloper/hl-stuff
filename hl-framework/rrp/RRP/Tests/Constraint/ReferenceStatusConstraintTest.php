<?php

namespace RRP\Tests\Constraint;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ApplicationStatuses;
use RRP\Constraint\ReferenceStatusConstraint;

/**
 * Class ReferenceStatusConstraintTest
 *
 * @package RRP\Tests\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceStatusConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function constraint_is_not_applied_when_reference_is_complete()
    {
        $reference = new ReferencingApplication();
        $reference->setStatus(ApplicationStatuses::COMPLETE);

        $mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferenceFromSession'))
            ->getMock();

        $mockSessionHolder->expects($this->once())
            ->method('getReferenceFromSession')
            ->with('123', '456')
            ->willReturn($reference);

        $constraint = new ReferenceStatusConstraint($mockSessionHolder);

        $this->assertTrue($constraint->verify('123', array('current_asn' => '456')));
    }

    /**
     * @test
     * @dataProvider getIncompleteReferences
     */
    public function constraint_is_applied_when_reference_is_incomplete(ReferencingApplication $reference)
    {
        $mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferenceFromSession'))
            ->getMock();

        $mockSessionHolder->expects($this->once())
            ->method('getReferenceFromSession')
            ->with('123', '456')
            ->willReturn($reference);

        $constraint = new ReferenceStatusConstraint($mockSessionHolder);

        $this->assertFalse($constraint->verify('123', array('current_asn' => '456')));
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function throws_exception_when_current_asn_is_not_a_key_in_data_array()
    {
        $stubSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->getMock();

        $constraint = new ReferenceStatusConstraint($stubSessionHolder);

        // The passed array does not pass the required 'current_asn' key.
        $constraint->verify('123', array('something_random' => '456'));
    }

    /**
     * Get incomplete references.
     *
     * @return array
     */
    public function getIncompleteReferences()
    {
        $cancelled = new ReferencingApplication();
        $cancelled->setStatus(ApplicationStatuses::CANCELLED);

        $declined = new ReferencingApplication();
        $declined->setStatus(ApplicationStatuses::DECLINED);

        $incomplete = new ReferencingApplication();
        $incomplete->setStatus(ApplicationStatuses::INCOMPLETE);

        $inProgress = new ReferencingApplication();
        $inProgress->setStatus(ApplicationStatuses::IN_PROGRESS);

        $awaitingAgent = new ReferencingApplication();
        $awaitingAgent->setStatus(ApplicationStatuses::AWAITING_AGENT_REVIEW);

        $awaitingDetails = new ReferencingApplication();
        $awaitingDetails->setStatus(ApplicationStatuses::AWAITING_APPLICATION_DETAILS);

        return array(
            array($cancelled),
            array($declined),
            array($incomplete),
            array($inProgress),
            array($awaitingAgent),
            array($awaitingDetails)
        );
    }
}