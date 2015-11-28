<?php

namespace Iris\Referencing\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Iris\Utility\Lookup\Lookup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormInterface;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;

/**
 * Class LettingRefereeType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class LettingRefereeType extends AbstractType
{
    /**
     * Phone blank message
     */
    const PHONE_BLANK_MESSAGE = 'Please enter either a daytime or evening phone number';

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
            ->add('type', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::LETTING_REFEREE_TYPE),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                )
            ))
            ->add('name', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 2,
                    ))
                )
            ))
            ->add('address', new AddressType(), array(
                'has_foreign_type' => true,
            ))
            ->add('dayPhone', 'text', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'dayphone',
                        'message' => self::PHONE_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'groups' => 'dayphone',
                        'min' => 9,
                    )),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getPhone() ?: 'nophone',
                        'message' => 'Telephone number must not be the same as tenant contact details',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Phone number is invalid',
                    )),
                ),
                'label' => 'Telephone (day)',
            ))
            ->add('eveningPhone', 'text', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'eveningphone',
                        'message' => self::PHONE_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'groups' => 'eveningphone',
                        'min' => 9,
                    )),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getPhone() ?: 'nophone',
                        'message' => 'Telephone number must not be the same as tenant contact details',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Phone number is invalid',
                    )),
                ),
                'label' => 'Telephone (evening)',
            ))
            ->add('fax', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 9,
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Fax number is invalid',
                    )),
                )
            ))
            ->add('email', 'email', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'email',
                    )),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getEmail() ?: 'noemail',
                        'message' => 'Email must not be the same as tenant contact details',
                    )),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\Common\Model\LettingReferee',

            /**
             * Customise the validation group used depending on submitted data
             *
             * @param FormInterface $form
             * @return array
             */
            'validation_groups' => function(FormInterface $form) {
                $dayPhoneData = $form->get('dayPhone')->getData();
                $eveningPhoneData = $form->get('eveningPhone')->getData();
                $emailData = $form->get('email')->getData();

                // All other fields are validated through the Default validation group
                $validation_groups = array('Default');

                /** @var ReferencingApplication $application */
                $application = $form->getParent()->getData();
                if ($application instanceof ReferencingApplication) {

                    // If Optimum product, require at least one contact detail must be given
                    if (19 == $application->getProductId()) {
                        if (empty($dayPhoneData) && empty($eveningPhoneData) && empty($emailData)) {
                            $validation_groups[] = 'dayphone';
                            $validation_groups[] = 'eveningphone';
                            $validation_groups[] = 'email';
                        }
                    }

                    // If no day phone, enforce evening
                    if (empty($dayPhoneData)) {
                        $validation_groups[] = 'eveningphone';
                    }

                    // If no evening phone, enforce day
                    if (empty($eveningPhoneData)) {
                        if ($k = array_search('eveningphone', $validation_groups)) {
                            unset($validation_groups[$k]);
                        }
                        $validation_groups[] = 'dayphone';
                    }

                    return $validation_groups;
                }

                return array('Default');
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'letting_referee_model';
    }
}
