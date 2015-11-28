<?php

namespace RRP\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RentRecoveryPlusSearchType
 *
 * @package RRP\Search
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('policyNumber', 'text', array(
                'required' => false,
            ))
            ->add('landlordName', 'text', array(
                'required' => false,
            ))
            ->add('propertyPostcode', 'text', array(
                'required' => false,
            ))
            ->add('resultsPerPage', 'choice', array(
                'required' => false,
                'choices' => array(
                    10 => '10',
                    25 => '25',
                    50 => '50',
                    100 => '100',
                ),
                'empty_value' => false,
                'data' => 10,
            ))
            ->setMethod('GET')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'data_class' => 'RRP\Model\RentRecoveryPlusSearchCriteria',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rent_recovery_plus_search';
    }
}