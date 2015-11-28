<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\AbstractReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\TenancyAgreement;
use Barbon\HostedApi\AppBundle\Form\Reference\Type\TenancyAgreementType;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class AbstractConfirmationController extends Controller
{
    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * @var Event
     */
    protected $confirmationEvent;

    /**
     * Constructor
     *
     * @param HttpKernelInterface $kernel
     * @param EventDispatcherInterface $eventDispatcher
     * @param IrisEntityManager $irisEntityManager
     * @param Event $confirmationEvent
     */
    public function __construct
    (
        HttpKernelInterface $kernel,
        EventDispatcherInterface $eventDispatcher,
        IrisEntityManager $irisEntityManager,
        Event $confirmationEvent
    )
    {
        $this->kernel = $kernel;
        $this->eventDispatcher = $eventDispatcher;
        $this->irisEntityManager = $irisEntityManager;
        $this->confirmationEvent = $confirmationEvent;
    }

    /**
     * @param array $applications
     * @return \Symfony\Component\Form\Form
     */
    protected function buildTenancyAgreementForm(array $applications)
    {
        $tenancyAgreement = new TenancyAgreement();
        $tenancyAgreement->setApplications($applications);

        return $this->createForm(new TenancyAgreementType(), $tenancyAgreement, array(
            'user_type' => 'landlord'
        ));
    }

    /**
     * @param AbstractReferencingApplication $application
     * @param $caseId
     */
    protected function persistMarketingPreferences(AbstractReferencingApplication $application, $caseId)
    {
        $this->irisEntityManager->persist($application, array(
            'caseId' => $caseId,
            'applicationId' => $application->getApplicationId(),
        ));
    }
}