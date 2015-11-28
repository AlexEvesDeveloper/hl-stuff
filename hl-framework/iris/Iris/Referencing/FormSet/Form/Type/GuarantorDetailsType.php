<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\BankAccountType;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Iris\Utility\Lookup\Lookup;
use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Symfony\Component\Form\FormBuilderInterface;
use Iris\Common\Titles;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Iris\Referencing\Form\Type\MoneyWithoutStringTransformerType;

/**
 * Class GuarantorDetailsType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class GuarantorDetailsType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'choice', array(
                'choices' => Titles::getTitles(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('firstName', 'text', array(
                'constraints' => array(
                    new Assert\Length(array('min' => 1)),
                    new Assert\NotBlank(),
                )
            ))
            ->add('middleName', 'text', array(
                'required' => false,
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Assert\Length(array('min' => 1)),
                    new Assert\NotBlank(),
                )
            ))
            ->add('otherName', 'text', array(
                'required' => false,
            ))
            ->add('birthDate', 'birthday')
            ->add('employmentStatus', 'choice', array(
                'label' => 'Current Employment Status',
                // TODO: limit choices depending on rent guarantee
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::EMPLOYMENT_STATUS),
                'empty_value' => '- Please Select -',
            ))
            ->add('grossIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Total Gross Annual Income',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array('value' => 0)),
                    new Assert\NotNull(array(
                        'message' => 'Please confirm the gross annual income',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                )
            ))
            ->add('bankAccount', new BankAccountType(), array())
            ->add('phone', 'text', array(
                'label' => 'Telephone Number',
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
                        'message' => 'Phone number is invalid',
                    )),
                )
            ))
            ->add('mobile', 'text', array(
                'label' => 'Mobile Number',
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
                        'message' => 'Phone number is invalid',
                    )),
                )
            ))
            ->add('email', 'email', array(
                'label' => 'Email Address',

                // Note: email is not required, but if it is supplied it
                // must be valid
                'required' => false,
                'constraints' => array(
                    new Assert\Email(),
                    new Assert\NotBlank(),
                )
            ))
            ->add('hasCCJ', 'checkbox', array(
                'label' => 'Any CCJs or adverse credit history?',
                'required' => false,
            ))
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
            'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',

            /**
             * Customise the validation group used depending on submitted data
             *
             * @param FormInterface $form
             * @return array
             */
            'validation_groups' => function(FormInterface $form) {
                $phoneData = $form->get('phone')->getData();
                $mobileData = $form->get('mobile')->getData();

                // All other fields are validated through the Default validation group
                $validation_groups = array('Default');

                // If both or neither phone and mobile fields are given,
                // validate both fields
                if (empty($phoneData) && empty($mobileData)) {
                    $validation_groups[] = 'phone';
                    $validation_groups[] = 'mobile';
                }
                else {
                    // If only phone field alone is given, validate, but
                    // not mobile. Only 1 is required.
                    if (!empty($phoneData)) {
                        $validation_groups[] = 'phone';
                    }

                    // If only mobile field alone is given, validate, but
                    // not phone. Only 1 is required.
                    if (!empty($mobileData)) {
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
        return 'tenant_details';
    }
}
