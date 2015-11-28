<?php

namespace Barbon\PaymentPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AddressType
 *
 * @package Barbon\PaymentPortalBundle\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\FormType
 */
class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lines', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
            ))
            ->add('town', 'text')
            ->add('county', 'text')
            ->add('postcode', 'text')
            ->add('country', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbon\PaymentPortalBundle\Entity\Address',
                'csrf_protection' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'address';
    }
}