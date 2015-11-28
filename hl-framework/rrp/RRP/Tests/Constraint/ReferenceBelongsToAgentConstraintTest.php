<?php

namespace RRP\Tests\Constraint;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResult;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use RRP\Constraint\ReferenceBelongsToAgentConstraint;

/**
 * Class ReferenceBelongsToAgentConstraintTest
 *
 * @package RRP\Tests\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceBelongsToAgentConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock_ClientRegistry
     */
    private $mockClientRegistry;

    /**
     * @var Mock_IndividualApplicationSearch
     */
    private $mockSearchClient;

    /**
     * @var Mock_ReferencingApplicationFindResults
     */
    private $mockSearchResults;

    /**
     * @var Mock_ReferencingApplicationClient
     */
    private $mockReferencingApplicationClient;

    /**
     * Pre test set up.
     */
    public function setUp()
    {
        // IndividualApplicationSearch mock
        $this->mockSearchClient = $this->getMockBuilder('Iris\IndividualApplication\Search\IndividualApplicationSearch')
            ->disableOriginalConstructor()
            ->setMethods(array('search'))
            ->getMock();

        // ReferencingApplicationFindResults mock
        $this->mockSearchResults = $this->getMockBuilder('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResults')
            ->setMethods(array('getTotalRecords', 'getRecords'))
            ->getMock();

        // Mock ClientRegistry->getAgentContext()->getReferencingApplicationClient()->getReferencingApplication() to return $reference.
        $this->mockClientRegistry = $this->getMockBuilder('Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry')
            ->setMethods(array('getAgentContext'))
            ->getMock();

        $mockAgentContext = $this->getMockBuilder('Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext')
            ->setMethods(array('getReferencingApplicationClient'))
            ->getMock();

        $this->mockReferencingApplicationClient = $this->getMockBuilder('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferencingApplication'))
            ->getMock();

        $this->mockClientRegistry->expects($this->any())
            ->method('getAgentContext')
            ->willReturn($mockAgentContext);

        $mockAgentContext->expects($this->any())
            ->method('getReferencingApplicationClient')
            ->willReturn($this->mockReferencingApplicationClient);
    }

    /**
     * @test
     */
    public function constraint_is_applied_when_search_returns_no_results()
    {
        $searchCriteria = new SearchIndividualApplicationsCriteria();
        $searchCriteria->setReferenceNumber('HLT999');

        $this->mockSearchResults->expects($this->once())
            ->method('getTotalRecords')
            ->willReturn(0);

        $this->mockSearchClient->expects($this->once())
            ->method('search')
            ->with('1234567', $searchCriteria)
            ->willReturn($this->mockSearchResults);

        $stubSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->getMock();

        $constraint = new ReferenceBelongsToAgentConstraint(
            $this->mockClientRegistry,
            $this->mockSearchClient,
            $searchCriteria,
            $stubSessionHolder
        );

        $this->assertFalse($constraint->verify('HLT999', array('current_asn' => '1234567')));
    }

    /**
     * The first found reference 'HLT888' will not match 'HLT999'
     *
     * @test
     */
    public function constraint_is_applied_when_first_reference_found_is_not_a_match()
    {
        $firstReferenceFound = new ReferencingApplicationFindResult();
        $firstReferenceFound->setReferenceNumber('HLT888');

        $searchCriteria = new SearchIndividualApplicationsCriteria();
        $searchCriteria->setReferenceNumber('HLT999');

        $this->mockSearchClient->expects($this->once())
            ->method('search')
            ->with('1234567', $searchCriteria)
            ->willReturn($this->mockSearchResults);

        $this->mockSearchResults->expects($this->once())
            ->method('getTotalRecords')
            ->willReturn(1);

        $this->mockSearchResults->expects($this->once())
            ->method('getRecords')
            ->willReturn(array($firstReferenceFound, new ReferencingApplicationFindResult()));

        $stubSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->getMock();

        $constraint = new ReferenceBelongsToAgentConstraint(
            $this->mockClientRegistry,
            $this->mockSearchClient,
            $searchCriteria,
            $stubSessionHolder
        );

        $this->assertFalse($constraint->verify('HLT999', array('current_asn' => '1234567')));
    }

    /**
     * @test
     */
    public function constraint_is_not_applied_when_first_reference_found_is_a_match()
    {
        $firstReferenceFound = new ReferencingApplicationFindResult();
        $firstReferenceFound->setReferenceNumber('HLT999');

        $searchCriteria = new SearchIndividualApplicationsCriteria();
        $searchCriteria->setReferenceNumber('HLT999');

        $this->mockSearchClient->expects($this->once())
            ->method('search')
            ->with('1234567', $searchCriteria)
            ->willReturn($this->mockSearchResults);

        $this->mockSearchResults->expects($this->once())
            ->method('getTotalRecords')
            ->willReturn(1);

        $this->mockSearchResults->expects($this->once())
            ->method('getRecords')
            ->willReturn(array($firstReferenceFound, new ReferencingApplicationFindResult()));

        $this->mockReferencingApplicationClient->expects($this->once())
            ->method('getReferencingApplication')
            ->willReturn(new ReferencingApplication());

        $stubSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->getMock();

        $constraint = new ReferenceBelongsToAgentConstraint(
            $this->mockClientRegistry,
            $this->mockSearchClient,
            $searchCriteria,
            $stubSessionHolder
        );

        $this->assertTrue($constraint->verify('HLT999', array('current_asn' => '1234567')));
    }

    /**
     * @test
     */
    public function reference_is_put_into_session_when_found()
    {
        // Assert that putReferenceInSession is called on a MockSessionHolder
        $firstReferenceFound = new ReferencingApplicationFindResult();
        $firstReferenceFound->setReferenceNumber('HLT999');

        $searchCriteria = new SearchIndividualApplicationsCriteria();
        $searchCriteria->setReferenceNumber('HLT999');

        $this->mockSearchClient->expects($this->once())
            ->method('search')
            ->with('1234567', $searchCriteria)
            ->willReturn($this->mockSearchResults);

        $this->mockSearchResults->expects($this->once())
            ->method('getTotalRecords')
            ->willReturn(1);

        $this->mockSearchResults->expects($this->once())
            ->method('getRecords')
            ->willReturn(array($firstReferenceFound, new ReferencingApplicationFindResult()));

        $this->mockReferencingApplicationClient->expects($this->once())
            ->method('getReferencingApplication')
            ->willReturn(new ReferencingApplication());

        $mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('putReferenceInSession'))
            ->getMock();

        $mockSessionHolder->expects($this->once())
            ->method('putReferenceInSession')
            ->with(new ReferencingApplication());

        $constraint = new ReferenceBelongsToAgentConstraint(
            $this->mockClientRegistry,
            $this->mockSearchClient,
            $searchCriteria,
            $mockSessionHolder
        );

        $constraint->verify('HLT999', array('current_asn' => '1234567'));
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

        $constraint = new ReferenceBelongsToAgentConstraint(
            $this->mockClientRegistry,
            $this->mockSearchClient,
            new SearchIndividualApplicationsCriteria(),
            $stubSessionHolder
        );

        // The passed array does not pass the required 'current_asn' key.
        $constraint->verify('123', array('something_random' => '456'));
    }
}