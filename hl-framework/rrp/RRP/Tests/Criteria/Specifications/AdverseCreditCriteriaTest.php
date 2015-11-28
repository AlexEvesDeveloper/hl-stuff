<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\CriteriaInterface;
use RRP\Criteria\Specifications\AdverseCreditCriteria;
use RRP\Model\RentRecoveryPlusReference;

/**
 * Class AdverseCreditCriteriaTest
 *
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class AdverseCreditCriteriaTest extends \PHPUnit_Framework_TestCase
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
        $this->criteria = new AdverseCreditCriteria();
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_reference_has_no_adverse_credit()
    {
        $reference = new ReferencingApplication();
        $reference->setHasCCJ(false);

        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        $this->assertTrue($this->criteria->isSatisfiedBy($rrpReference));
    }

    /**
     * @test
     */
    public function criteria_is_not_satisfied_when_reference_has_adverse_credit()
    {
        $reference = new ReferencingApplication();
        $reference->setHasCCJ(true);

        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        $this->assertFalse($this->criteria->isSatisfiedBy($rrpReference));
    }
}