<?php

namespace RRP\Tests\Criteria;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\Plus;

/**
 * Class PlusTest
 *
 * @package RRP\Tests\Criteria
 * @author Alex Eves <alex.eves@barbon.com>
 */
class PlusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function criteria_is_satisfied_when_both_criteria_return_true()
    {
        $mockCriteriaOne = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaOne->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(true));
        $mockCriteriaTwo = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaTwo->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(true));

        $stubReference = $this->getMock('RRP\Model\RentRecoveryPlusReference');
        $plusObj = new Plus($mockCriteriaOne, $mockCriteriaTwo);

        $this->assertTrue($plusObj->isSatisfiedBy($stubReference));
    }

    /**
     * @test
     */
    public function criteria_is_not_satisfied_when_first_criteria_returns_false()
    {
        $mockCriteriaOne = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaOne->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(false));
        $mockCriteriaTwo = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaTwo->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(true));

        $stubReference = $this->getMock('RRP\Model\RentRecoveryPlusReference');
        $plusObj = new Plus($mockCriteriaOne, $mockCriteriaTwo);

        $this->assertFalse($plusObj->isSatisfiedBy($stubReference));
    }

    /**
     * @test
     */
    public function criteria_is_not_satisfied_when_second_criteria_returns_false()
    {
        $mockCriteriaOne = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaOne->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(true));
        $mockCriteriaTwo = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaTwo->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(false));

        $stubReference = $this->getMock('RRP\Model\RentRecoveryPlusReference');
        $plusObj = new Plus($mockCriteriaOne, $mockCriteriaTwo);

        $this->assertFalse($plusObj->isSatisfiedBy($stubReference));
    }
    /**
     * @test
     */
    public function criteria_is_not_satisfied_when_both_criteria_return_false()
    {
        $mockCriteriaOne = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaOne->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(false));
        $mockCriteriaTwo = $this->getMock('RRP\Criteria\CriteriaInterface');
        $mockCriteriaTwo->expects($this->once())->method('isSatisfiedBy')->will($this->returnValue(false));

        $stubReference = $this->getMock('RRP\Model\RentRecoveryPlusReference');
        $plusObj = new Plus($mockCriteriaOne, $mockCriteriaTwo);

        $this->assertFalse($plusObj->isSatisfiedBy($stubReference));
    }
}