<?php

namespace Iris\Referencing\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\EmploymentStatusOptions;
use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Iris\Referencing\Form\EventListener\FinancialRefereeDegradationListener;
use Iris\Referencing\FormSet\Form\Type\AbstractFormStepType;
use Iris\Utility\Lookup\Lookup;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FinancialRefereeType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class FinancialRefereeType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * Accountant ID, mapped to categories from FINANCIAL_REFEREE_TYPE lookup.
     */
    const ACCOUNTANT = 1;

    /**
     * Employer ID, mapped to categories from FINANCIAL_REFEREE_TYPE lookup.
     */
    const EMPLOYER = 2;

    /**
     * Pension Administrator ID, mapped to categories from FINANCIAL_REFEREE_TYPE lookup.
     */
    const PENSION_ADMINISTRATOR = 3;

    /**
     * Pension administrator ID, mapped to categories from FINANCIAL_REFEREE_TYPE lookup.
     */
    const PENSION_STATEMENT = 4;

    /**
     * Self assessment ID, mapped to categories from FINANCIAL_REFEREE_TYPE lookup.
     */
    const SELF_ASSESSMENT = 5;

    /**
     * @var string
     */
    const COMPANY_NAME_BLANK_MESSAGE = 'Please enter company name';
    const COMPANY_NAME_LESS_THAN_MIN_CHARS_MESSAGE = 'Company name must be at least {{ limit }} characters long';

    const CONTACT_NAME_BLANK_MESSAGE = 'Please enter contact name';
    const CONTACT_NAME_LESS_THAN_MIN_CHARS_MESSAGE = 'Contact name must be at least {{ limit }} characters long';

    const CONTACT_POSITION_BLANK_MESSAGE = 'Please enter contact position';
    const CONTACT_POSITION_LESS_THAN_MIN_CHARS_MESSAGE = 'Contact position must be at least {{ limit }} characters long';

    const PHONE_BLANK_MESSAGE = 'Please enter a telephone number';
    const PHONE_LESS_THAN_MIN_CHARS_MESSAGE = 'Phone number must be at least {{ limit }} characters long';
    const FAX_LESS_THAN_MIN_CHARS_MESSAGE = 'Fax number must be at least {{ limit }} characters long';

    const EMAIL_NOT_VALID_MESSAGE = 'Email address is not valid';

    const APPLICANT_POSITION_HELD_BLANK_MESSAGE = 'Please enter position held by applicant';
    const APPLICANT_POSITION_HELD_LESS_THAN_MIN_CHARS_MESSAGE = 'Position held by applicant must be at least {{ limit }} characters long';
    const APPLICANT_POSITION_HELD_LESS_THAN_MAX_CHARS_MESSAGE = 'Position held by applicant must be no more than {{ limit }} characters long';

    const IS_PERMANENT_BLANK_MESSAGE = 'Please select if the position is permanent';

    const PAYROLL_NUMBER_LESS_THAN_MIN_CHARS_MESSAGE = 'Payroll number must be at least {{ limit }} characters long';

    const APPLICANT_ANNUAL_INCOME_BLANK_MESSAGE = 'Please enter applicant annual income';
    const APPLICANT_ANNUAL_INCOME_INVALID = 'Applicant annual income is not valid';

    const FINANCIAL_REFEREE_START_DATE_INVALID = 'Start date invalid';

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

        // Get the financial referee type choices
        $financialRefereeTypeChoices = Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::FINANCIAL_REFEREE_TYPE);

        // If this is the current employer, limit choices depending on employment status
        if (isset($options['refereeStatus']) && 1 == $options['refereeStatus']) {
            $financialRefereeTypeChoices = $this->truncateFinancialRefereeChoicesByEmploymentStatus(
                $financialRefereeTypeChoices,
                $currentApplication->getEmploymentStatus()
            );
        }

        // At first we display only a type selection, as the rest of the form makes no sense until it's been selected.
        // On the front end, selecting the type automatically POSTs the form and serverside when this happens the
        //   validation of the remaining fields must be suppressed.
        $builder
            // In front end make the type choice submit the form (without validating it) to show the correct fields.
            ->add('financialRefereeType', 'choice', array(
                'choices' => $financialRefereeTypeChoices,
                'empty_value' => '- Please Select -',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => ' This should not be blank',
                    )),
                ),
            ))
            ->add('update', 'submit', array(
                'attr' => array(
                    'value' => '1',
                ),
            ))

            // These fields all exist so their data can persist in the form as a user switches the type around, but
            //   start off disabled and hidden, and enabled in the SUBMIT listener.
            ->add('companyName', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'referee_details',
                        'message' => self::COMPANY_NAME_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'min' => 2,
                        'minMessage' => self::COMPANY_NAME_LESS_THAN_MIN_CHARS_MESSAGE,
                        'groups' => 'referee_details',
                    ))
                )
            ))
            ->add('address', new AddressType(), array(
                'has_foreign_type' => true,
            ))
            ->add('contactName', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'referee_details',
                        'message' => self::CONTACT_NAME_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'min' => 2,
                        'groups' => 'referee_details',
                        'minMessage' => self::CONTACT_NAME_LESS_THAN_MIN_CHARS_MESSAGE,
                    ))
                )
            ))
            ->add('contactPosition', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'referee_details',
                        'message' => self::CONTACT_POSITION_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'min' => 2,
                        'groups' => 'referee_details',
                        'minMessage' => self::CONTACT_POSITION_LESS_THAN_MIN_CHARS_MESSAGE,
                    ))
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'Telephone (day)',
                // Required field, must be at least 9 chars long
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'referee_details',
                        'message' => self::PHONE_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'min' => 9,
                        'groups' => 'contact_details',
                        'minMessage' => self::PHONE_LESS_THAN_MIN_CHARS_MESSAGE,
                    )),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getPhone() ?: 'nophone',
                        'message' => 'Telephone number must not be the same as tenant contact details',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Phone number is invalid',
                    )),
                )
            ))
            ->add('fax', 'text', array(
                // Not required field, but if supplied must be at least 9 chars long
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 9,
                        'groups' => 'contact_details',
                        'minMessage' => self::FAX_LESS_THAN_MIN_CHARS_MESSAGE,
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}+$/',
                        'message' => 'Fax number is invalid',
                    )),
                ),
                'required' => false,
            ))
            ->add('email', 'email', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Email(array(
                        'groups' => 'contact_details',
                        'message' => self::EMAIL_NOT_VALID_MESSAGE,
                    )),
                    new Assert\NotEqualTo(array(
                        'value' => $currentApplication->getEmail() ?: 'noemail',
                        'message' => 'Email must not be the same as tenant contact details',
                    )),
                ),
            ))
            ->add('applicantPositionHeld', 'text', array(
                'label' => 'Position Held by Applicant',
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'referee_details',
                        'message' => self::APPLICANT_POSITION_HELD_BLANK_MESSAGE,
                    )),
                    new Assert\Length(array(
                        'min' => 2,
                        'max' => 45,
                        'groups' => 'applicant_details',
                        'minMessage' => self::APPLICANT_POSITION_HELD_LESS_THAN_MIN_CHARS_MESSAGE,
                        'maxMessage' => self::APPLICANT_POSITION_HELD_LESS_THAN_MAX_CHARS_MESSAGE,
                    ))
                )
            ))
            ->add('isPermanent', new BooleanExpandedType(), array(
                'label' => 'Is the position permanent?',
                'required' => false,
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'applicant_details',
                        'message' => self::IS_PERMANENT_BLANK_MESSAGE,
                    ))
                )
            ))
            ->add('payrollNumber', 'text', array(
                'label' => 'Payroll/Service/Pension/National Insurance Number',
            ))
            ->add('applicantAnnualIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Gross Annual Salary', // Note label uses "salary"
                'currency' => 'GBP',
                //'precision' => 0,
                'constraints' => array(
                    new Assert\NotNull(array(
                        'groups' => 'income',
                        'message' => self::APPLICANT_ANNUAL_INCOME_BLANK_MESSAGE,
                    )),
                    new Assert\Regex(array(
                        'groups' => 'income',
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                )
            ))
            ->add('employmentStartDate', 'date', array(
                'label' => 'Start Date',
                'years' => self::getEmploymentStartDateYears(),
                'constraints' => array(
                    new Assert\Date(array(
                        'groups' => 'applicant_position_commencement',
                        'message' => self::FINANCIAL_REFEREE_START_DATE_INVALID,
                    ))
                )
            ))

            // Extra questions that drive logic in further forms
            ->add('employmentChangeDuringTenancy', new BooleanExpandedType(), array(
                'label' => 'Will this employment change before or during the tenancy?',
                'required' => false,
            ))
            ->add('multipleJobOrPension', new BooleanExpandedType(), array(
                'label' => 'Select if you have more than one job or are in receipt of a pension',
                'required' => false,
            ))
        ;

        // Correct initialise the status Id depending on options passed
        $financialRefereeStatusOptions = array();

        if (null !== $options['refereeStatus']) {
            $financialRefereeStatusOptions['empty_data'] = $options['refereeStatus'];
            $financialRefereeStatusOptions['data'] = $options['refereeStatus'];
        }

        $builder
            ->add('financialRefereeStatus', 'hidden', $financialRefereeStatusOptions)
        ;

        // Listener to control inclusion of fields
        $builder->addEventSubscriber(new FinancialRefereeDegradationListener());
    }

    /**
     * Get employment start date years (100 years previous
     * from current year and 2 years in future)
     *
     * @return array
     */
    public static function getEmploymentStartDateYears()
    {
        $currentYear = (int)date('Y');
        $years = range($currentYear + 2, $currentYear - 100);
        return array_combine($years, $years);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbondev\IRISSDK\Common\Model\FinancialReferee',
                'refereeStatus' => null,
                'validation_groups' => function(FormInterface $form) {
                    // All other fields are validated through the Default validation group

                    $validation_groups = array('Default');
                    $model = $form->getData();

                    $financialRefereeType = $model->getFinancialRefereeType();

                    // If there is no financial referee ID type set, don't try to control validation any further
                    if ('' == $financialRefereeType) {
                        return $validation_groups;
                    }

                    switch ($financialRefereeType) {
                        case FinancialRefereeType::ACCOUNTANT:
                            $validation_groups[] = 'contact_details';
                            $validation_groups[] = 'referee_details';
                            $validation_groups[] = 'income';
                            $validation_groups[] = 'applicant_position_commencement';

                            break;

                        case FinancialRefereeType::PENSION_STATEMENT:
                            $validation_groups[] = 'income';
                            break;

                        case FinancialRefereeType::PENSION_ADMINISTRATOR:
                            $validation_groups[] = 'contact_details';
                            $validation_groups[] = 'referee_details';
                            $validation_groups[] = 'income';
                            $validation_groups[] = 'pension_details';
                            break;

                        case FinancialRefereeType::SELF_ASSESSMENT:
                            $validation_groups[] = 'income';
                            $validation_groups[] = 'applicant_position_commencement';
                            break;

                        case FinancialRefereeType::EMPLOYER:
                            $validation_groups[] = 'contact_details';
                            $validation_groups[] = 'referee_details';
                            $validation_groups[] = 'applicant_details';
                            $validation_groups[] = 'income';
                            $validation_groups[] = 'applicant_position_commencement';
                            break;
                    }


                    return $validation_groups;
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['suppress_errors'] = false;
        if ($this->isUpdateClicked($form)) {
            $view->vars['suppress_errors'] = true;
        }
    }

    /**
     * Return TRUE if the form is being updated
     *
     * @param FormInterface $form
     * @return bool
     */
    private function isUpdateClicked(FormInterface $form)
    {
        /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $currentFinancialReferee */
        $currentFinancialReferee = $form->getData();

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $requestParams = \Zend_Registry::get('iris_container')->get('request')->request->all();

        if (isset($requestParams['step']['step']['financialReferees']) && is_array($requestParams['step']['step']['financialReferees'])) {

            foreach ($requestParams['step']['step']['financialReferees'] as $financialReferee) {

                if ($currentFinancialReferee->getFinancialRefereeType() == $financialReferee['financialRefereeType']) {

                    if (isset($financialReferee['update']) && $financialReferee['update']) {

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Truncate the list of choices for financial referee type based on the
     * applicant's employment status
     *
     * @param array $financialRefereeTypeChoices
     * @param int $employmentStatus
     * @return array
     */
    private function truncateFinancialRefereeChoicesByEmploymentStatus(array $financialRefereeTypeChoices, $employmentStatus)
    {
        switch ($employmentStatus) {

            case EmploymentStatusOptions::EMPLOYED:
                $financialRefereeTypeChoices = $this->unsetAllChoicesByKeyExcept($financialRefereeTypeChoices, array(
                    self::EMPLOYER,
                ));
                break;

            case EmploymentStatusOptions::SELF_EMPLOYED:
                $financialRefereeTypeChoices = $this->unsetAllChoicesByKeyExcept($financialRefereeTypeChoices, array(
                    self::ACCOUNTANT,
                    self::SELF_ASSESSMENT,
                ));
                break;

            case EmploymentStatusOptions::RETIRED:
                $financialRefereeTypeChoices = $this->unsetAllChoicesByKeyExcept($financialRefereeTypeChoices, array(
                    self::PENSION_ADMINISTRATOR,
                    self::PENSION_STATEMENT,
                ));
                break;

            case EmploymentStatusOptions::ON_CONTRACT:
                $financialRefereeTypeChoices = $this->unsetAllChoicesByKeyExcept($financialRefereeTypeChoices, array(
                    self::EMPLOYER
                ));
                break;
        }

        return $financialRefereeTypeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'financial_referee';
    }
}
