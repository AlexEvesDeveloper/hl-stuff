<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

abstract class AbstractContactDetailsConstraintSubscriber implements EventSubscriberInterface, NotInArrayConstraintSubscriber
{
    /**
     * @var mixed
     */
    private $unacceptableValues = array();

    /**
     * @var NotInArray
     */
    protected $constraint = null;

    /**
     * Add a value that the field must not match
     *
     * @param $value
     */
    public function addValue($value)
    {
        $this->unacceptableValues[] = $value;
    }

    /**
     * Get the constraint
     *
     * @return NotInArray
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * POST_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        if (null !== $this->constraint) {
            $this->constraint->notInArray = $this->unacceptableValues;
        }
    }
}
