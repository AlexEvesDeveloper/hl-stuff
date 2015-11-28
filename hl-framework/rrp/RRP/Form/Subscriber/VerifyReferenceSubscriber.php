<?php

namespace RRP\Form\Subscriber;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use Iris\IndividualApplication\Search\IndividualApplicationSearch;
use RRP\Constraint\ConstraintInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class VerifyReferenceSubscriber
 *
 * @package RRP\Form\Subscriber
 * @author Alex Eves <alex.eves@barbon.com>
 */
class VerifyReferenceSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $preSubmitConstraints;

    /**
     * @var array
     */
    protected $postSubmitConstraints;

    /**
     * @var string
     */
    protected $currentAsn;

    /**
     * VerifyReferenceSubscriber constructor
     */
    public function __construct()
    {
        $this->preSubmitConstraints = array();
        $this->postSubmitConstraints = array();
    }

    /**
     * Get subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SUBMIT =>  'onPostSubmit'
        );
    }

    /**
     * Acts as a composite for the pre submit constraints applied to this subscribers. Verifies the reference number against each constraint.
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $value = $event->getData();

        // Provide a quick return for an empty value against pre submit constraints.
        if (empty($value)) {
            $form->addError(new FormError('Please enter a reference number'));
            return;
        }

        // Verify the reference number entered on the form.
        foreach ($this->preSubmitConstraints as $constraint) {
            if (false === $constraint->verify($value, array('current_asn' => $this->getCurrentAsn()))) {
                $form->addError(new FormError($constraint->getErrorText()));
                break;
            }
        }

        return;
    }

    /**
     * Acts as a composite for the post submit constraints applied to this subscribers. Verifies the reference number against each constraint.
     *
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $value = $event->getData();

        // Verify the reference number entered on the form against post submit constraints.
        foreach ($this->postSubmitConstraints as $constraint) {
            if (false === $constraint->verify($value, array('current_asn' => $this->getCurrentAsn()))) {
                $form->addError(new FormError($constraint->getErrorText()));
            }
        }

        return;
    }

    /**
     * Add a pre submit constraint to the collection.
     *
     * @param ConstraintInterface $constraint
     */
    public function addPreSubmitConstraint(ConstraintInterface $constraint)
    {
        $this->preSubmitConstraints[] = $constraint;
    }

    /**
     * Add a post submit constraint to the collection.
     *
     * @param ConstraintInterface $constraint
     */
    public function addPostSubmitConstraint(ConstraintInterface $constraint)
    {
        $this->postSubmitConstraints[] = $constraint;
    }

    /**
     * Get $currentAsn
     *
     * @return string
     */
    public function getCurrentAsn()
    {
        return $this->currentAsn;
    }

    /**
     * Set $currentAsn
     *
     * @param string $currentAsn
     * @return VerifyReferenceSubscriber
     */
    public function setCurrentAsn($currentAsn)
    {
        $this->currentAsn = $currentAsn;
        return $this;
    }
}