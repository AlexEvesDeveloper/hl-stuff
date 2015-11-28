<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\FormSet\Form\EventListener\FinancialRefereesRegistrationListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Iris\Referencing\Form\Type\FinancialRefereeType;

/**
 * Class FinancialRefereesType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class FinancialRefereesType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('financialReferees', 'collection', array(
                    'type' => new FinancialRefereeType(),

                    // Prevent the collection type from interfering with the form
                    'allow_add' => false,    // Don't add form fields for us, we'll do it
                    'allow_delete' => false, // Don't remove empty form fields
                    'delete_empty' => false, // Don't remove empty form fields
                ))->addEventSubscriber(new FinancialRefereesRegistrationListener())
            )
        ;
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
        return 'financial_referees';
    }
}
