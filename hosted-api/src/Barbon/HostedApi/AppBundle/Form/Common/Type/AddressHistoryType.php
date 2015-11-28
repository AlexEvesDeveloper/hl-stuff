<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\AddressHistory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

final class AddressHistoryType extends AbstractType
{
    /**
     * @var FormTypeInterface
     */
    private $addressHistoryCollectionType;

    /**
     * Constructor
     *
     * @param FormTypeInterface $addressHistoryCollectionType
     */
    public function __construct(FormTypeInterface $addressHistoryCollectionType)
    {
        $this->addressHistoryCollectionType = $addressHistoryCollectionType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressHistories', 'collection', array(
                'type' => $this->addressHistoryCollectionType,
                'prototype_name' => '__previousaddressname__',

                'allow_add' => true,
                'allow_delete' => true,

                'options' => array(
                    'read_only_except_postcode' => true,
                    'label' => false,
                ),

                'constraints' => array(
                    new AddressHistory(array(
                        // Valid if any of the following conditions are met
                        'maxAddresses' => 3, // Three addresses have been supplied
                        'maxDuration' => 36, // Total address period is three years
                        'stopAtForeign' => true,
                    )),
                )
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
