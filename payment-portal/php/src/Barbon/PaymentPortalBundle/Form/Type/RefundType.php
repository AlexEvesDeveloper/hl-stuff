<?php

namespace Barbon\PaymentPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RefundType
 *
 * @package Barbon\PaymentPortalBundle\Form\Type
 * @author April Portus <april.portus@barbon.com>
 *
 * @DI\FormType
 */
class RefundType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'number')
            ->add('currency', 'currency')
            ->add('transId', 'text')
            ->add('paymentStatusCallbackUrl', 'text')
            ->add('paymentStatusCallbackPayload', 'text')
            ->add('redirectOnSuccessUrl', 'text')
            ->add('redirectOnFailureUrl', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbon\PaymentPortalBundle\Entity\CustomerOrder',
                'csrf_protection' => false,
                'cascade_validation' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'refund';
    }
}