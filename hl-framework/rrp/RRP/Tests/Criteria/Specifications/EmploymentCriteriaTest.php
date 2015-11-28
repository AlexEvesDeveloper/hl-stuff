<?php

namespace RRP\Tests\Criteria\Specifications;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\EmploymentStatuses;
use RRP\Criteria\CriteriaInterface;
use RRP\Criteria\Specifications\EmploymentCriteria;
use RRP\Model\RentRecoveryPlusReference;


/**
 * Class EmploymentCriteriaTest
 *
 * @package RRP\Tests\Criteria\Specifications
 * @author Alex Eves <alex.eves@barbon.com>
 */
class EmploymentCriteriaTest extends \PHPUnit_Framework_TestCase
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
        $this->criteria = new EmploymentCriteria();
    }

    /**
     * @test
     */
    public function criteria_is_satisfied_when_tenant_is_employed()
    {
        $reference = new ReferencingApplication();
        $reference->setEmploymentStatus(EmploymentStatuses::EMPLOYED);

        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        $this->assertTrue($this->criteria->isSatisfiedBy($rrpReference));
    }

    /**
     * @test
     * @dataProvider getUnemployedReferences
     */
    public function criteria_is_not_satisfied_when_tenant_is_not_employed(RentRecoveryPlusReference $reference)
    {
        $this->assertFalse($this->criteria->isSatisfiedBy($reference));
    }

    /**
     * @return array
     */
    public function getUnemployedReferences()
    {
        $unemployedStatuses = array(
            'unemployed' => EmploymentStatuses::UNEMPLOYED,
            'independent' => EmploymentStatuses::INDEPENDENT_MEANS,
            'contract' => EmploymentStatuses::ON_CONTRACT,
            'retired' => EmploymentStatuses::RETIRED,
            'student' => EmploymentStatuses::STUDENT,
            'self-employed' => EmploymentStatuses::SELF_EMPLOYED
        );

        $references = array();
        foreach ($unemployedStatuses as $key => $unemployedStatus) {
            $reference = new ReferencingApplication();
            $reference->setEmploymentStatus($unemployedStatus);

            $rrpReference = new RentRecoveryPlusReference();
            $rrpReference->setParent($reference);
            $references[$key] = $rrpReference;
        }

        return array(
            array($references['unemployed']),
            array($references['independent']),
            array($references['contract']),
            array($references['retired']),
            array($references['student']),
            array($references['self-employed']),
        );
    }
}