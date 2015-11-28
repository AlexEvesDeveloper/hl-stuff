<?php

namespace Iris\Referencing\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Iris\Common\Titles;
use Iris\Utility\Lookup\Lookup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LandlordType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class LandlordType extends AbstractType
{
    /**
     * Phone not blank message
     */
    const PHONE_NOT_BLANK_MESSAGE = 'Please enter either a daytime or evening telephone number';

    /**
     * Maximum phone number length
     */
    const PHONE_NUMBER_MAX_LENGTH = 14;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Get the current application
        $currentApplication = \Zend_Registry::get('iris_container')
            ->get('iris.referencing.application.current_form_flow_records')
            ->getApplication()
        ;

        $builder
            ->add('title', 'choice', array(
                'choices' => Titles::getTitles(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please select a title',
                    )),
                ),
            ))
            ->add('firstName', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please enter first name',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[-a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric and spaces only',
                    ))
                )
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please enter last name',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric and spaces only',
                    ))
                )
            ))
            ->add('address', new AddressType())
            ->add('dayPhone', 'text', array(
                'label' => 'Telephone (day)',
                'constraints' => array(
                    new Assert\Length(array('max' => self::PHONE_NUMBER_MAX_LENGTH)),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Phone number is invalid',
                    )),
                ),
            ))
            ->add('eveningPhone', 'text', array(
                'label' => 'Telephone (evening)',
                'constraints' => array(
                    new Assert\Length(array('max' => self::PHONE_NUMBER_MAX_LENGTH)),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Phone number is invalid',
                    )),
                ),
            ))
            ->add('fax', 'text', array(
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Fax number is invalid',
                    )),
                ),
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new Assert\Email(),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getEmail() ?: 'noemail',
                        'message' => 'Email must not be the same as tenant contact details',
                    )),
                )
            ))
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

                $form = $event->getForm();
                $data = $event->getData();

                // If day phone or eve phone don't exist
                if (!$data['dayPhone'] && !$data['eveningPhone']) {

                    $form
                        ->add('dayPhone', 'text', array(
                            'constraints' => array(
                                new Assert\NotBlank(array(
                                    'message' => LandlordType::PHONE_NOT_BLANK_MESSAGE,
                                )),
                                new Assert\Length(array('max' => LandlordType::PHONE_NUMBER_MAX_LENGTH)),
                            ),
                            'label' => 'Telephone (day)',
                        ))
                        ->add('eveningPhone', 'text', array(
                            'constraints' => array(
                                new Assert\NotBlank(array(
                                    'message' => LandlordType::PHONE_NOT_BLANK_MESSAGE,
                                )),
                                new Assert\Length(array('max' => LandlordType::PHONE_NUMBER_MAX_LENGTH)),
                            ),
                            'label' => 'Telephone (evening)',
                        ))
                    ;
                }

                // If day phone not there, constrain evening
                elseif (!$data['dayPhone']) {

                    $form
                        ->add('eveningPhone', 'text', array(
                            'constraints' => array(
                                new Assert\NotBlank(),
                                new Assert\Length(array('max' => LandlordType::PHONE_NUMBER_MAX_LENGTH)),
                            ),
                            'label' => 'Telephone (evening)',
                        ))
                    ;
                }

                // If evening phone not there, constrain day
                elseif (!$data['eveningPhone']) {

                    $form
                        ->add('dayPhone', 'text', array(
                            'constraints' => array(
                                new Assert\NotBlank(),
                                new Assert\Length(array('max' => LandlordType::PHONE_NUMBER_MAX_LENGTH)),
                            ),
                            'label' => 'Telephone (day)',
                        ))
                    ;
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
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'landlord';
    }
}