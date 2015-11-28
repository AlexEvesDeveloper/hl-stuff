<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber interface for event subscribers implementing the Bridge/Constraint
 * design pattern.
 */
interface BridgingEventSubscriberInterface extends EventSubscriberInterface
{
    /**
     * Get the constraint event subscriber
     *
     * @return mixed
     */
    public function getConstraintSubscriber();
}
