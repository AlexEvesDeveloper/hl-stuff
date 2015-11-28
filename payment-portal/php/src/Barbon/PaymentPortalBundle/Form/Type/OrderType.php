<?php

namespace Barbon\PaymentPortalBundle\Form\Type;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderType
 * Note: Should really be PaymentType but because it was implemented without forethought we're stuck with it!
 *
 * @package Barbon\PaymentPortalBundle\Form\Type
 * @author Ashley Dawson <ashley@ashleydawson.co.uk>
 *
 * @DI\FormType
 */
class OrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'number')
            ->add('currency', 'currency')
            ->add('paymentTypes', 'choice', array(
                'choices' => array(
                    CustomerOrder::PAYMENT_TYPE_CARD_PAYMENT => 'Card Payment',
                    CustomerOrder::PAYMENT_TYPE_DIRECT_DEBIT => 'Direct Debit',
                ),
                'multiple' => true,
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('payer', 'payer')
            ->add('paymentStatusCallbackUrl', 'text')
            ->add('paymentStatusCallbackPayload', 'text')
            ->add('redirectOnSuccessUrl', 'text')
            ->add('redirectOnFailureUrl', 'text')
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                if ( ! isset($data['payer'])) {
                    $event->getForm()->remove('payer');
                }
            })
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
        return 'order';
    }
}