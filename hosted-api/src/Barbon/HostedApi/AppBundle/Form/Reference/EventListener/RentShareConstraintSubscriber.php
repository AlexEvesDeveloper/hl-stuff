<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Barbon\HostedApi\AppBundle\Traits\SessionModelRetrieverTrait;
use Barbon\HostedApi\AppBundle\Validator\Reference\Constraints\RentShare;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Event subscriber to adjust the constraints on the application rent share field, based
 * on the case data submitted in a request.
 */
final class RentShareConstraintSubscriber implements EventSubscriberInterface
{
    use SessionModelRetrieverTrait;

    /**
     * @var float
     */
    private $totalRent = null;

    /**
     * @var RentShare
     */
    private $rentShareConstraint = null;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Set the case total rent
     *
     * @param $totalRent float Total rent of case
     */
    public function setTotalRent($totalRent)
    {
        $this->totalRent = $totalRent;
    }

    /**
     * Get the rent share constraint
     *
     * @return RentShare
     */
    public function getRentShareConstraint()
    {
        if (null === $this->rentShareConstraint) {
            $this->rentShareConstraint = new RentShare(array(
                'blankMessage' => 'Please enter a share of rent',
                'invalidMessage' => 'Please enter a positive numeric value for share of rent',
                'invalidFloatMessage' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"',
                'moreThanTotalRentMessage' => 'This amount must be less than the total rent',
            ));
        }
        
        return $this->rentShareConstraint;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        if (null !== $this->rentShareConstraint) {
            // totalRent is set through a subscriber to the totalRent field on the ReferencingCaseType.
            // Therefore when we add a new application to an existing case, we don't trigger the subscriber, so haven't get the total rent.
            // In that instance, attempt to retrieve a case from the session
            if (null === $this->totalRent) {
                $case = $this->getCase($this->session);
                $this->totalRent = $case->getTotalRent();
            }

            if (null !== $this->totalRent) {
                $this->rentShareConstraint->totalRent = $this->totalRent;
            }
        }
    }
}
