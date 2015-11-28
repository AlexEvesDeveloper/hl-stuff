<?php

namespace Iris\Utility\RentAffordability\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RentAffordabilityType
 *
 * @package Iris\Utility\RentAffordability\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class RentAffordabilityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monthlyRent', 'money', array(
                'required' => false,
                'currency' => 'GBP',
                'precision' => 0,
            ))
            ->add('tenantAnnualIncome', 'money', array(
                'required' => false,
                'currency' => 'GBP',
                'precision' => 0,
            ))
            ->add('guarantorAnnualIncome', 'money', array(
                'required' => false,
                'currency' => 'GBP',
                'precision' => 0,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rent_affordability';
    }
}