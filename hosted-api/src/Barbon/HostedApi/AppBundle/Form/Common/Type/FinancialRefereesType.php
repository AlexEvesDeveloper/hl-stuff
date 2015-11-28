<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

final class FinancialRefereesType extends AbstractType
{
    private $financialRefereeCollectionType;

    public function __construct(FormTypeInterface $financialRefereeCollectionType)
    {
        $this->financialRefereeCollectionType = $financialRefereeCollectionType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('financialReferees', 'collection', array(
                    'type' => $this->financialRefereeCollectionType,
                    'prototype_name' => '__financialrefereename__',

                    // Prevent the collection type from interfering with the form
                    'allow_add' => false,    // Don't add form fields for us, we'll do it
                    'allow_delete' => false, // Don't remove empty form fields
                    'delete_empty' => false, // Don't remove empty form fields

                    'options' => array(
                        'label' => false,
                        'phone_constraint_subscriber' => $options['phone_constraint_subscriber'],
                        'email_constraint_subscriber' => $options['email_constraint_subscriber'],
                    ),
                ))

// todo: consider a better solution than the listener that has been developed
//                    ->addEventSubscriber(new FinancialRefereesRegistrationListener())

            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'phone_constraint_subscriber' => null,
            'email_constraint_subscriber' => null,
        ));

        $resolver->setAllowedTypes(array(
            'phone_constraint_subscriber' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            'email_constraint_subscriber' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'financial_referees';
    }
}
