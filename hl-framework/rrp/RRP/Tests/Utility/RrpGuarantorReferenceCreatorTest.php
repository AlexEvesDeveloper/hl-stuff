<?php

namespace RRP\Tests\Utility;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingDecisionDetails;
use Guzzle\Common\Collection;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Utility\RrpGuarantorReferenceCreator;


/**
 * Class RrpGuarantorReferenceCreatorTest
 *
 * @package RRP\Tests\Utility
 * @author Alex Eves <alex.eves@barbon.com>
 */
class RrpGuarantorReferenceCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock_ClientRegistry
     */
    protected $mockClientRegistry;

    /**
     * @var Mock_ReferencingApplicationClient
     */
    protected $mockReferencingApplicationClient;

    /**
     * @var Mock_DecisionDetailsRetriever
     */
    protected $mockDecisionDetailsRetriever;

    /**
     * Mock the web service call to IRIS. Doing so involves mocking the chained outcomes, beginning at the \ClientRegistry.
     */
    public function setUp()
    {
        // Mock the chain: $clientRegistry->getAgentContext()->getReferencingApplicationClient()->getReferencingApplicationGuarantors()
        $this->mockClientRegistry = $this->getMockBuilder('Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry')
            ->setMethods(array('getAgentContext'))
            ->getMock();

        $mockAgentContext = $this->getMockBuilder('Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext')
            ->setMethods(array('getReferencingApplicationClient'))
            ->getMock();

        $this->mockReferencingApplicationClient = $this->getMockBuilder('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferencingApplicationGuarantors'))
            ->getMock();

        $this->mockClientRegistry->expects($this->once())
            ->method('getAgentContext')
            ->willReturn($mockAgentContext);

        $mockAgentContext->expects($this->once())
            ->method('getReferencingApplicationClient')
            ->willReturn($this->mockReferencingApplicationClient);

        // Mock the chain: $decisionDetailsRetriever->getDecisionDetails()
        $this->mockDecisionDetailsRetriever = $this->getMockBuilder('RRP\Utility\DecisionDetailsRetriever')
            ->disableOriginalConstructor()
            ->setMethods(array('getDecisionDetails'))
            ->getMock();

        $this->mockDecisionDetailsRetriever->expects($this->once())
            ->method('getDecisionDetails')
            ->willReturn(new ReferencingDecisionDetails());
    }

    /**
     * @test
     * @covers RrpGuarantorReferenceCreator::getGuarantor
     * @covers RrpGuarantorReferenceCreator::createGuarantor
     */
    public function a_guarantor_is_retrieved_given_a_tenant_reference()
    {
        $reference = new ReferencingApplication();
        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        $mockResult = new Collection(array(new ReferencingApplication()));
        $this->mockReferencingApplicationClient->expects($this->once())
            ->method('getReferencingApplicationGuarantors')
            ->willReturn($mockResult);

        $rrpGuarantorReferenceCreator = new RrpGuarantorReferenceCreator($this->mockClientRegistry, $this->mockDecisionDetailsRetriever);

        $this->assertInstanceOf('RRP\Model\RentRecoveryPlusReference', $rrpGuarantorReferenceCreator->getGuarantor($rrpReference));
    }

    /**
     * @test
     * @covers RrpGuarantorReferenceCreator::getGuarantor
     * @covers RrpGuarantorReferenceCreator::createGuarantor
     */
    public function guarantor_is_only_retrieved_from_web_service_once_on_consecutive_calls()
    {
        $reference = new ReferencingApplication();
        $rrpReference = new RentRecoveryPlusReference();
        $rrpReference->setParent($reference);

        // Prepare the first call which will talk to web service
        $mockResult = new Collection(array(new ReferencingApplication()));
        $this->mockReferencingApplicationClient->expects($this->once())
            ->method('getReferencingApplicationGuarantors')
            ->willReturn($mockResult);

        $rrpGuarantorReferenceCreator = new RrpGuarantorReferenceCreator($this->mockClientRegistry, $this->mockDecisionDetailsRetriever);
        $rrpGuarantorReferenceCreator->getGuarantor($rrpReference);

        // Second time around, assert that we don't talk to web service, and still return a RentRecoveryPlusReference
        $this->mockClientRegistry->expects($this->never())
            ->method($this->anything());

        $this->assertInstanceOf('RRP\Model\RentRecoveryPlusReference', $rrpGuarantorReferenceCreator->getGuarantor($rrpReference));
    }
}