<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TatCallMeType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class TatCallMeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mobileNumber', 'text', array(
                'label' => 'Mobile number',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'groups' => 'mobile',
                        'min' => 11
                    )),

                    // Required to ensure the field is not blank, which is needed
                    // due to the turning off the required status of the field
                    // which is required to turn off client side JS validation
                    // code
                    new Assert\NotBlank(array(
                        'groups' => 'mobile',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'This value is not a valid mobile number.',
                    )),
                )
            ))
            ->add('landlineNumber', 'text', array(
                'label' => 'Landline number',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'groups' => 'phone',
                        'min' => 9
                    )),

                    // Required to ensure the field is not blank, which is needed
                    // due to the turning off the required status of the field
                    // which is required to turn off client side JS validation
                    // code
                    new Assert\NotBlank(array(
                        'groups' => 'phone',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'This value is not a valid landline number.',
                    )),
                )
            ))
            ->add('additionalInfo', 'textarea', array(
                'label' => 'Any additional information',
                'required' => false,
                'attr' => array(
                    'class' => 'text form-control',
                    'rows' => '24',
                    'cols' => '80',
                ),
            ))
            ->add('timeToCall', 'choice', array(
                'label' => 'When is the best time to call',
                'required' => true,
                'choices' => array(
                    'Anytime' => 'Any time',
                    'Morning' => 'Morning',
                    'Afternoon' => 'Afternoon',
                    'Evening' => 'Evening',
                ),
                'attr' => array(
                    'class' => 'text form-control',
                ),
            ))
            ->add('Back', 'submit')
            ->add('Submit', 'submit')
        ;
    }

    /**
     * Set form options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            /**
             * Customise the validation group used depending on submitted data
             *
             * @param FormInterface $form
             * @return array
             */
            'validation_groups' => function(FormInterface $form) {
                    $landlineNumberData = $form->get('landlineNumber')->getData();
                    $mobileNumberData = $form->get('mobileNumber')->getData();

                    // All other fields are validated through the Default validation group
                    $validation_groups = array('Default');

                    // If both or neither phone and mobile fields are given,
                    // validate both fields
                    if (empty($landlineNumberData) && empty($mobileNumberData)) {
                        $validation_groups[] = 'phone';
                        $validation_groups[] = 'mobile';
                    }
                    else {
                        // If only phone field alone is given, validate, but
                        // not mobile. Only 1 is required.
                        if (!empty($landlineNumberData)) {
                            $validation_groups[] = 'phone';
                        }

                        // If only mobile field alone is given, validate, but
                        // not phone. Only 1 is required.
                        if (!empty($mobileNumberData)) {
                            $validation_groups[] = 'mobile';
                        }
                    }

                    return $validation_groups;
                }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tat_callMe';
    }
}