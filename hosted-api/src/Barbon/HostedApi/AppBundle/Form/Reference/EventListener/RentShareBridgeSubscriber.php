<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;

/**
 * Bridge a field value with the rent share subscriber
 */
final class RentShareBridgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var RentShareConstraintSubscriber
     */
    private $applicationRentShareSubscriber;

    /**
     * Constructor
     *
     * @param RentShareConstraintSubscriber $applicationRentShareSubscriber
     */
    public function __construct(RentShareConstraintSubscriber $applicationRentShareSubscriber)
    {
        $this->applicationRentShareSubscriber = $applicationRentShareSubscriber;
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
        $totalRent = $event->getData();
        $this->applicationRentShareSubscriber->setTotalRent($totalRent);
    }
}
