<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\DateRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class PreviousAddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isVisible', 'hidden', array(
                'required' => false,
                'attr' => array(
                    'class' => 'is-visible'
                )
            ))
            ->add('isForeign', 'checkbox', array(
                'label' => 'If the address is outside of the UK please tick the box',
                'required' => false,
                'attr' => array(
                    'class' => 'abroad-toggle'
                )
            ))
            ->add('address', new AddressType(), array(
                'read_only_except_postcode' => $options['read_only_except_postcode'],
            ))
            ->add('startDate', 'date', array(
                'years' => range(date('Y'), date('Y') - 100),
                'placeholder' => '--',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new DateRange(array(
                        'min' => '00:00:00 -100 YEAR',
                        'max' => '00:00:00',
                        'minMessage' => 'The start date must have been within the last 100 years.',
                        'maxMessage' => 'The start date cannot be in the future.',
                        'invalidMessage' => 'The start date must be a valid date',
                    ))
                ),
                'attr' => array(
                    'data-provide' => 'datepicker',
                    'data-end-date' => date('d/m/Y')
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress',
            'read_only_except_postcode' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'previous_address';
    }
}
