<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\Form\Type\BankAccountType;
use Iris\Referencing\Form\Type\BooleanExpandedType;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Iris\Utility\Lookup\Lookup;
use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Iris\Validator\Constraints\DateRange;
use Symfony\Component\Form\FormBuilderInterface;
use Iris\Common\Titles;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Iris\Referencing\Form\Type\MoneyWithoutStringTransformerType;

/**
 * Class TenantDetailsType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class TenantDetailsType extends AbstractFormStepType implements StepTypeInterface
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
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('middleName', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]*$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                ),
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array('fullValidation'),
                        'message' => 'Please enter a last name',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('otherName', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^[a-zA-Z0-9\w]*$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                ),
            ))
            ->add('birthDate', 'birthday', array(
                'constraints' => array(
                    new DateRange(array(
                        'min' => '-121 YEARS',
                        'max' => '-18 YEARS',
                        'maxMessage' => 'Applicant must be older than 18 years of age',
                    )),
                ),
            ))
            ->add('residentialStatus', 'choice', array(
                'label' => 'Current Residential Status',
                'choices' => self::getResidentialStatusChoices(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please select a current residential status',
                    )),
                ),
            ))
            ->add('employmentStatus', 'choice', array(
                'label' => 'Current Employment Status',
                // TODO: limit choices depending on rent guarantee
                'choices' => self::getEmploymentStatusChoices(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please select a current employment status',
                    )),
                ),
            ))
            ->add('grossIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Total Gross Annual Income',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 0,
                        'message' => 'Please enter a positive numeric value for share of rent',
                    )),
                    new Assert\NotBlank(array(
                        'message' => 'Please enter a total gross annual income',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    )),
                )
            ))
            ->add('bankAccount', new BankAccountType())
            ->add('phone', 'text', array(
                'label' => 'Telephone Number',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'groups' => 'phone',
                        'min' => 9,
                        'minMessage' => 'Please provide at least 9 numeric characters',
                    )),
                    new Assert\NotBlank(array(
                        'groups' => 'phone',
                        'message' => 'Please provide a telephone number',
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
                        'min' => 11,
                        'minMessage' => 'Please provide at least 11 numeric characters',
                    )),
                    new Assert\NotBlank(array(
                        'groups' => 'mobile',
                        'message' => 'Please provide a mobile number',
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
                    new Assert\Email(array(
                        'message' => 'Please provide a valid email address'
                    )),
                )
            ))
            ->add('hasCCJ', new BooleanExpandedType(), array(
                'label' => 'Any CCJs or adverse credit history?',
                'required' => false,
            ))
        ;

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

                $form = $event->getForm();

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                $application = $event->getData();
                if ($application instanceof ReferencingApplication) {

                    // Try to get the product from the application
                    $product = $application->getProduct();
                    if ($product instanceof Product) {

                        // Modify choices to exclude "Unknown" option on some occasions
                        $residentialStatusChoices = TenantDetailsType::getResidentialStatusChoices();
                        $employmentStatusChoices = TenantDetailsType::getEmploymentStatusChoices();

                        if (!in_array($product->getProductCode(), array(
                            'INSG', // Not Insight
                            'INRG', // Not Xpress
                        ))) {

                            // Remove the unknown choices
                            unset($residentialStatusChoices[5]);
                        }

                        if (!in_array($product->getProductCode(), array(
                            'INSG', // Not Insight
                        ))) {

                            // Remove the unknown choices
                            unset($employmentStatusChoices[8]);
                        }

                        $form
                            ->add('residentialStatus', 'choice', array(
                                'label' => 'Current Residential Status',
                                'choices' => $residentialStatusChoices,
                                'empty_value' => '- Please Select -',
                                'constraints' => array(
                                    new Assert\NotBlank(array(
                                        'message' => 'Please select a current residential status',
                                    )),
                                ),
                            ))
                            ->add('employmentStatus', 'choice', array(
                                'label' => 'Current Employment Status',
                                'choices' => $employmentStatusChoices,
                                'empty_value' => '- Please Select -',
                                'constraints' => array(
                                    new Assert\NotBlank(array(
                                        'message' => 'Please select a current employment status',
                                    )),
                                ),
                            ))
                        ;

                        // Prune unnecessary forms
                        // Only display this field if not Insight and not a guarantor
                        if (in_array($product->getProductCode(), array(
                            'INSG', // Insight
                        ))) {
                            //$form->remove('residentialStatus'); // todo: this causes errors in IRIS validation
                        }

                        // Prune unnecessary forms
                        // Only display this field if not Insight
                        if (in_array($product->getProductCode(), array(
                            'INSG', // Insight
                        ))) {
                            //$form->remove('hasEmploymentChanged'); // todo: this causes errors in IRIS validation
                        }

                        // Prune unnecessary forms
                        // Only display if not credit reference only product
                        if (in_array($product->getProductCode(), array(
                            'INSG', // Insight
                        ))) {
                            $form->remove('grossIncome');
                        }


                    }
                }

            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

                $data = $event->getData();
                $form = $event->getForm();

                if (isset($data['employmentStatus'])) {

                    // Remove not blank constraint gross income if student or unemployed
                    switch ($data['employmentStatus']) {
                        case 3: // Student
                        case 7: // Unemployed
                            $form
                                ->add('grossIncome', new MoneyWithoutStringTransformerType(), array(
                                    'label' => 'Total Gross Annual Income',
                                    'currency' => 'GBP',
                                    'constraints' => array(
                                        new Assert\GreaterThanOrEqual(array(
                                            'value' => 0,
                                            'message' => 'Please enter a positive numeric value for share of rent',
                                        )),
                                        new Assert\Regex(array(
                                            'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                                            'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                                        )),
                                    )
                                ))
                            ;
                            break;
                    }
                }

            })
        ;
    }

    /**
     * Get residential status choices
     *
     * @return array
     */
    public static function getResidentialStatusChoices()
    {
        return Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::RESIDENTIAL_STATUS);
    }

    /**
     * Get employment status choices
     *
     * @return array
     */
    public static function getEmploymentStatusChoices()
    {
        return Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::EMPLOYMENT_STATUS);
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

                $sortCodeData = $form->get('bankAccount')->get('accountSortcode');
                $accountNumberData = $form->get('bankAccount')->get('accountNumber');

                // If either the sort code or account no are given,
                // validate both fields
                if (!empty($sortCodeData) || !empty($accountNumberData)) {
                    $validation_groups[] = 'bankaccount';
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
