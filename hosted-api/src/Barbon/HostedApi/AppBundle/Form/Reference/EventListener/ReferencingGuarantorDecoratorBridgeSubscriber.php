<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ReferencingGuarantorDecoratorBridgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferencingGuarantorDecoratorSubscriber
     */
    private $guarantorDecoratorSubscriber;
    
    
    /**
     * Constructor
     * 
     * @param $guarantorDecoratorSubscriber
     */
    public function __construct(ReferencingGuarantorDecoratorSubscriber $guarantorDecoratorSubscriber)
    {
        $this->guarantorDecoratorSubscriber = $guarantorDecoratorSubscriber;
    }

    /**
     * Clone
     */
    public function __clone()
    {
        $this->guarantorDecoratorSubscriber = clone $this->guarantorDecoratorSubscriber;
    }

    /**
     * Get the field subscriber
     *
     * @return EventSubscriberInterface
     */
    public function getGuarantorDecorator()
    {
        return $this->guarantorDecoratorSubscriber;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'setFilter',
            FormEvents::PRE_SUBMIT => 'setFilter',
        );
    }

    /**
     * PRE_SET_DATA & PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function setFilter(FormEvent $event)
    {
        $productId = $event->getData();
        $this->guarantorDecoratorSubscriber->setProductId($productId);
    }
}
