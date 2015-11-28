<?php

namespace RRP\Tests\Form\DataTransformer;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;
use RRP\Form\DataTransformer\ReferenceNumberToReferenceObjectTransformer;
use RRP\Model\RentRecoveryPlusReference;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;

/**
 * Class ReferenceNumberToReferenceObjectTransformerTest
 *
 * @package RRP\Tests\Form\DataTransformer
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceNumberToReferenceObjectTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock_DecisionDetailsRetriever
     */
    protected $mockDecisionDetailsRetriever;

    /**
     * @var Mock_SessionReferenceHolder
     */
    protected $mockSessionHolder;

    /**
     * @var Mock_RentRecoveryPlusReference
     */
    protected $mockRentRecoveryPlusReference;

    /**
     * Pre test set up
     */
    public function setUp()
    {
        $this->mockDecisionDetailsRetriever = $this->getMockBuilder('RRP\Utility\DecisionDetailsRetriever')
            ->disableOriginalConstructor()
            ->setMethods(array('getDecisionDetails'))
            ->getMock();

        $this->mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferenceFromSession'))
            ->getMock();

        $this->mockRentRecoveryPlusReference = $this->getMockBuilder('RRP\Model\RentRecoveryPlusReference')
            ->setMethods(array('setParent', 'setDecisionDetails'))
            ->getMock();
    }

    /**
     * @test
     */
    public function displays_nothing_before_transformation()
    {
        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $this->assertNull($transformer->transform(null));
    }

    /**
     * @test
     */
    public function displays_reference_number_after_transformation()
    {
        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $parentReference = new ReferencingApplication();
        $parentReference->setReferenceNumber('HLT999');
        $transformedObject = new RentRecoveryPlusReference();
        $transformedObject->setParent($parentReference);

        $this->assertEquals('HLT999', $transformer->transform($transformedObject));
    }

    /**
     * @test
     */
    public function returns_null_when_no_matching_reference_is_stored_in_session()
    {
        $this->mockSessionHolder
            ->expects($this->once())
            ->method('getReferenceFromSession')
            ->willReturn(false);

        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $this->assertNull($transformer->reverseTransform('HLT999'));
    }

    /**
     * @test
     */
    public function transforms_a_matching_reference_number_into_a_reference_object()
    {
        $referenceFoundInSession = new ReferencingApplication();

        // We will find a valid reference in the session...
        $this->mockSessionHolder
            ->expects($this->once())
            ->method('getReferenceFromSession')
            ->willReturn($referenceFoundInSession);

        // And we will use it to retrieve a ReferencingDecisionDetails object.
        $this->mockDecisionDetailsRetriever
            ->method('getDecisionDetails')
            ->with($referenceFoundInSession)
            ->willReturn(new ReferencingDecisionDetails());

        // We will then set the reference found in session as the parent of the new object (the one we're transforming in to).
        $this->mockRentRecoveryPlusReference
            ->method('setParent')
            ->will($this->returnSelf());

        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $this->assertInstanceOf('RRP\Model\RentRecoveryPlusReference', $transformer->reverseTransform('HLT999'));
    }

    /**
     * @test
     */
    public function sets_the_parent_reference_on_the_new_object()
    {
        $referenceFoundInSession = new ReferencingApplication();

        // Set up the necessary scenarios for finding a valid reference in the session...
        $this->mockSessionHolder
            ->expects($this->once())
            ->method('getReferenceFromSession')
            ->willReturn($referenceFoundInSession);

        // And for getting its corresponding ReferencingDecisionDetails object...
        $this->mockDecisionDetailsRetriever
            ->method('getDecisionDetails')
            ->with($referenceFoundInSession)
            ->willReturn(new ReferencingDecisionDetails());

        // And then we can assert that 'setParent' will be called on the RentRecoveryPlusReference object.
        $this->mockRentRecoveryPlusReference
            ->expects($this->once())
            ->method('setParent')
            ->will($this->returnSelf());

        // Trigger the test
        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $transformer->reverseTransform('HLT999');
    }

    /**
     * @test
     */
    public function gets_and_sets_the_decision_details_on_the_new_object()
    {
        $referenceFoundInSession = new ReferencingApplication();

        // Set up the necessary scenarios for finding a valid reference in the session...
        $this->mockSessionHolder
            ->expects($this->once())
            ->method('getReferenceFromSession')
            ->willReturn($referenceFoundInSession);

        // And assert that 'getDecisionDetails' will be called on the $decisionDetailsRetriever...
        $this->mockDecisionDetailsRetriever
            ->expects($this->once())
            ->method('getDecisionDetails')
            ->with($referenceFoundInSession)
            ->willReturn(new ReferencingDecisionDetails());

        // And also assert that 'setDecisionDetails' will be called on the RentRecoveryPlusReference object.
        $this->mockRentRecoveryPlusReference
            ->method('setParent')
            ->will($this->returnSelf());
        $this->mockRentRecoveryPlusReference
            ->expects($this->once())
            ->method('setDecisionDetails');

        // Trigger the test
        $transformer = new ReferenceNumberToReferenceObjectTransformer(
            $this->mockDecisionDetailsRetriever,
            $this->mockSessionHolder,
            $this->mockRentRecoveryPlusReference
        );

        $transformer->reverseTransform('HLT999');
    }
}