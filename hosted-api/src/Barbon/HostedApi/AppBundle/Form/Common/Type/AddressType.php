<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\AddressValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\Postcode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class AddressType extends AbstractType
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
                    new Constraints\Length(array(
                        'min' => 0,
                        'max' => 32,
                    )),
                    new Constraints\NotBlank(array(
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
                    new Constraints\Length(array(
                        'min' => 0,
                        'max' => 80,
                    )),
                    new Constraints\NotBlank(array(
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
                    new Constraints\Length(array(
                        'min' => 0,
                        'max' => 32,
                    )),
                    new Constraints\NotBlank(array(
                        'groups' => array(
                            'propertyIdentifier',
                        ),
                        'message' => self::PROPERTY_ID_NOT_BLANK_MESSAGE,
                    )),
                ),
            ))
            ->add('street', 'text', array(
                'required' => false,
                'attr' => $attributes,
                'constraints' => array(
                    new Constraints\NotBlank(array(
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
                    new Constraints\NotBlank(array(
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
            ->add('country', 'country', array(
                'attr' => $attributes,
                'empty_value' => '- Please Select -'
            ))
            ->add('postcode', 'text', array(
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => self::POSTCODE_NOT_BLANK_MESSAGE
                    )),
                    new Postcode(array(
                        'message' => 'Postcode is not valid, must be e.g. NE63 9UD',
                        'groups' => 'postcode',
                    ))
                )
            ))
        ;

        // If this form is meant to have the foreign checkbox, include it
        if ($options['is_international_type']) {
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
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\Address',
            'read_only_except_postcode' => false,
            'is_international_type' => false,

            'validation_groups' => function (FormInterface $form) {
                $validationGroup = new AddressValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
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
