<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Iris\Validator\Constraints as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AddressType
 *
 * @package Iris\Referencing\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AddressType extends AbstractType
{
    /**
     * Constant message for property identifier not blank constraints
     */
    const PROPERTY_ID_NOT_BLANK_MESSAGE = 'Please enter flat, house name or house number';

    /**
     * Constant message for street not blank constraint
     */
    const STREET_NOT_BLANK_MESSAGE = 'Please enter street';

    /**
     * Constant message for town not blank constraint
     */
    const TOWN_NOT_BLANK_MESSAGE = 'Please enter town';

    /**
     * Constant message for postcode not blank constraint
     */
    const POSTCODE_NOT_BLANK_MESSAGE = 'Please enter postcode';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributes = array();
        if ($options['read_only_except_postcode']) {
            $attributes = array(
                'readonly' => 'readonly',
                'class' => 'readonly',
            );
        }

        $builder
            ->add('flat', 'text', array(
                'required' => false,
                'attr' => $attributes,
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 0,
                        'max' => 32,
                    )),
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'propertyIdentifier',
                        ),
                        'message' => self::PROPERTY_ID_NOT_BLANK_MESSAGE,
                    )),
                ),
            ))
            ->add('houseName', 'text', array(
                'required' => false,
                'attr' => $attributes,
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 0,
                        'max' => 80,
                    )),
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'propertyIdentifier',
                        ),
                        'message' => self::PROPERTY_ID_NOT_BLANK_MESSAGE,
                    )),
                ),
            ))
            ->add('houseNumber', 'text', array(
                'required' => false,
                'attr' => $attributes,
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 0,
                        'max' => 32,
                    )),
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'propertyIdentifier',
                        ),
                        'message' => self::PROPERTY_ID_NOT_BLANK_MESSAGE,
                    )),
                ),
            ))
            ->add('street', 'text', array(
                'attr' => $attributes,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => self::STREET_NOT_BLANK_MESSAGE
                    )),
                ),
            ))
            ->add('locality', 'text', array(
                'required' => false,
                'attr' => $attributes,
            ))
            ->add('town', 'text', array(
                'attr' => $attributes,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => self::TOWN_NOT_BLANK_MESSAGE
                    )),
                ),
            ))

            // Note:
            // Field type country applies locale selection based on the visitor.
            // If the locale configuration:
            //      intl.default_locale = 'en'
            // ...is not set within the php.ini, a different default is applied.
            // As a result, an exception will be raised when a browser configured
            // for 'en' visits due to unsupported locale type.
            ->add('country', 'choice', array(
                'attr' => $attributes,
                'choices' => array(
                    'United Kingdom' => 'United Kingdom',
                ),
                'data' => 'United Kingdom',
                'empty_value' => false,
            ))
            ->add('postcode', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => self::POSTCODE_NOT_BLANK_MESSAGE
                    )),
                    new CustomAssert\Postcode(array(
                        'message' => 'Postcode is not valid, must be e.g. NE63 9UD',
                        'groups' => 'postcode',
                    ))
                )
            ))
        ;

        // If this form is meant to have the foreign checkbox, include it
        if ($options['has_foreign_type']) {
            $builder->add('foreign', 'checkbox', array(
                'label' => 'Is this a Foreign Address?',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\Common\Model\Address',
            'read_only_except_postcode' => false,
            'has_foreign_type' => false,

            /**
             * Add validation based on form completion
             *
             * @param FormInterface $form
             * @return array
             */
            'validation_groups' => function (FormInterface $form) {
                if (
                    !$form->get('flat')->getData() &&
                    !$form->get('houseName')->getData() &&
                    !$form->get('houseNumber')->getData()
                ) {
                    return array(
                        'Default',
                        'propertyIdentifier',
                        'postcode',
                    );
                }
                return array(
                    'Default',
                    'postcode',
                );
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'address';
    }
}