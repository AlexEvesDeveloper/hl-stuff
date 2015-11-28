<?php

namespace Barbon\PaymentPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PayerType
 *
 * @package Barbon\PaymentPortalBundle\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\FormType
 */
class PayerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('telephone', 'text')
            ->add('billingAddress', 'address')
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                if ( ! isset($data['billingAddress'])) {
                    $event->getForm()->remove('billingAddress');
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
                'data_class' => 'Barbon\PaymentPortalBundle\Entity\Payer',
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
        return 'payer';
    }
}