<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

abstract class AbstractFinancialRefereeContactDetailsConstraintSubscriber implements EventSubscriberInterface, NotInArrayConstraintSubscriber
{
    /**
     * @var mixed
     */
    private $unacceptableValues = array();

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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * Get the field name to apply constraints to
     *
     * @return mixed
     */
    abstract protected function getFieldName();

    /**
     * POST_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->has($this->getFieldName())) {
            $options = $form->get($this->getFieldName())->getConfig()->getAttribute('data_collector/passed_options');

            if (isset($options['constraints']) && is_array($options['constraints'])) {
                // Find the rent share constraint
                foreach ($options['constraints'] as $constraint) {
                    if ($constraint instanceof NotInArray && null !== $this->unacceptableValues) {
                        // If found, set unacceptable array list to our collection
                        $constraint->notInArray = $this->unacceptableValues;
                        break;
                    }
                }
            }
        }
    }
}
