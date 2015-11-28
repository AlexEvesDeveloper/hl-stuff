<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NotInArrayBridgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var NotInArrayConstraintSubscriber[]
     */
    private $subscribers = array();

    /**
     * Add a constraint subscriber to the subscriber
     *
     * @param NotInArrayConstraintSubscriber $notInArrayConstraintSubscriber
     */
    public function addConstraintSubscriber(NotInArrayConstraintSubscriber $notInArrayConstraintSubscriber)
    {
        if (null === $notInArrayConstraintSubscriber) {
            throw new InvalidArgumentException('Parameter $notInArrayConstraintSubscriber cannot be null');
        }

        $this->subscribers[] = $notInArrayConstraintSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    /**
     * PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        
        foreach ($this->subscribers as $subscriber) {
            $subscriber->addValue($data);
        }
    }
}
