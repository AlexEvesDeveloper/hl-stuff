<?php

namespace RRP\Tests\Constraint;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Constraint\ReferenceExpiryConstraint;

/**
 * Class ReferenceExpiryConstraintTest
 *
 * @package RRP\Tests\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceExpiryConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function constraint_is_not_applied_when_reference_has_not_expired()
    {
        // The ReferencingApplication object will store the date in milliseconds, so mulitply by 1000 when setting.
        $nonExpiredDate = strtotime('-60 days', time());
        $reference = new ReferencingApplication();
        $reference->setFirstCompletionAt($nonExpiredDate * 1000);

        $mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferenceFromSession'))
            ->getMock();

        $mockSessionHolder->expects($this->once())
            ->method('getReferenceFromSession')
            ->with('123', '456')
            ->willReturn($reference);

        $constraint = new ReferenceExpiryConstraint($mockSessionHolder);

        $this->assertTrue($constraint->verify('123', array('current_asn' => '456')));
    }

    /**
     * @test
     */
    public function constraint_is_applied_when_reference_has_expired()
    {
        // The ReferencingApplication object will store the date in milliseconds, so mulitply by 1000 when setting.
        $expiredDate = strtotime('-61 days', time());
        $reference = new ReferencingApplication();
        $reference->setFirstCompletionAt($expiredDate * 1000);

        $mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferenceFromSession'))
            ->getMock();

        $mockSessionHolder->expects($this->once())
            ->method('getReferenceFromSession')
            ->with('123', '456')
            ->willReturn($reference);

        $constraint = new ReferenceExpiryConstraint($mockSessionHolder);

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

        $constraint = new ReferenceExpiryConstraint($stubSessionHolder);

        // The passed array does not pass the required 'current_asn' key.
        $constraint->verify('123', array('something_random' => '456'));
    }
}