<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\PreviousAddressType;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Iris\Validator\Constraints\AddressHistory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AddressHistoryType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class AddressHistoryType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressHistories', 'collection', array(
                'type' => new PreviousAddressType(),
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'read_only_except_postcode' => true,
                ),
                'constraints' => array(
                    new AddressHistory(array(
                        // Valid if any of the following conditions are met
                        'maxAddresses' => 3, // Three addresses have been supplied
                        'maxDuration' => 36, // Total address period is three years
                        'stopAtForeign' => true, // A foreign address is given
                    )),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Test to see if the address history constraint has been violated to let the view know
        $addressHistoryConstraintViolation = false;

        // Look through form errors for violation
        $formErrors = $form->getParent()->getErrors();
        foreach($formErrors as $key => $formError) {
            if (
                method_exists($formError->getCause(), 'getCode') &&
                'ADDRESSHISTORYVIOLATION' == $formError->getCause()->getCode()
            ) {
                $addressHistoryConstraintViolation = true;
            }
        }

        $view->vars['addressHistoryConstraintViolation'] = $addressHistoryConstraintViolation;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'address_history';
    }
}