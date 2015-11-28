<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\FinancialRefereeType;
use Barbon\HostedApi\AppBundle\Form\Common\Type\AddressType;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints;

class FinancialRefereeDecoratorSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormTypeInterface
     */
    private $addressType;

    /**
     * @var NotInArrayConstraintSubscriber
     */
    private $notApplicantPhoneConstraintSubscriber;

    /**
     * @var NotInArrayConstraintSubscriber
     */
    private $notApplicantEmailConstraintSubscriber;

    /**
     * @param FormTypeInterface $addressType
     * @param NotInArrayConstraintSubscriber $notApplicantPhoneConstraintSubscriber
     * @param NotInArrayConstraintSubscriber $notApplicantEmailConstraintSubscriber
     */
    public function __construct(
        FormTypeInterface $addressType,
        NotInArrayConstraintSubscriber $notApplicantPhoneConstraintSubscriber,
        NotInArrayConstraintSubscriber $notApplicantEmailConstraintSubscriber
    )
    {
        $this->addressType = $addressType;
        $this->notApplicantPhoneConstraintSubscriber = $notApplicantPhoneConstraintSubscriber;
        $this->notApplicantEmailConstraintSubscriber = $notApplicantEmailConstraintSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    /**
     * PRE_SET_DATA handler
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $financialRefereeType = $event->getData();
        $form = $event->getForm()->getParent();
        $this->decorateForm($financialRefereeType, $form);
    }

    /**
     * PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $financialRefereeType = $event->getData();
        $form = $event->getForm()->getParent();

        // Remove all form fields for re-adding
        $form
            ->remove('companyName')
            ->remove('contactName')
            ->remove('contactPosition')
            ->remove('phone')
            ->remove('fax')
            ->remove('email')
            ->remove('address')
            ->remove('applicantPositionHeld')
            ->remove('isPermanent')
            ->remove('payrollNumber')
            ->remove('applicantAnnualIncome')
            ->remove('applicantAnnualPension')
            ->remove('applicantAnnualEarnings')
            ->remove('employmentStartDate')
        ;

        $this->decorateForm($financialRefereeType, $form);
    }

    /**
     * Decorate the form with extra fields, depending on the referee type
     *
     * @param $financialRefereeType
     * @param FormInterface $form
     */
    private function decorateForm($financialRefereeType, FormInterface $form)
    {
        switch ($financialRefereeType) {
            case FinancialRefereeType::ACCOUNTANT:
                $this
                    ->addCompanyName($form)
                    ->addContactName($form)
                    ->addContactPosition($form)
                    ->addPhone($form)
                    ->addFax($form)
                    ->addEmail($form)
                    ->addAddress($form)
                    ->addApplicantAnnualIncome($form)
                    ->addEmploymentStartDate($form)
                ;
                break;

            case FinancialRefereeType::EMPLOYER:
                $this
                    ->addCompanyName($form)
                    ->addContactName($form)
                    ->addContactPosition($form)
                    ->addPhone($form)
                    ->addFax($form)
                    ->addEmail($form)
                    ->addAddress($form)
                    ->addIsPermanent($form)
                    ->addPayrollNumber($form)
                    ->addApplicantPositionHeld($form)
                    ->addApplicantAnnualIncome($form)
                    ->addEmploymentStartDate($form)
                ;
                break;

            case FinancialRefereeType::PENSION_ADMINISTRATOR:
                $this
                    ->addCompanyName($form)
                    ->addContactName($form)
                    ->addContactPosition($form)
                    ->addPhone($form)
                    ->addFax($form)
                    ->addEmail($form)
                    ->addAddress($form)
                    ->addApplicantAnnualPension($form)
                ;
                break;

            case FinancialRefereeType::PENSION_STATEMENT:
                $this
                    ->addApplicantAnnualPension($form)
                    ->addEmploymentStartDate($form)
                ;
                break;

            case FinancialRefereeType::SELF_ASSESSMENT:
                $this
                    ->addApplicantAnnualEarnings($form)
                    ->addEmploymentStartDate($form)
                ;
                break;
        }
    }

    /**
     * Add the company name field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addCompanyName(FormInterface $form)
    {
        $form->add('companyName', 'text', array(
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'referee_details',
                    'message' => 'Please enter company name',
                )),
                new Constraints\Length(array(
                    'min' => 2,
                    'minMessage' => 'Company name must be at least {{ limit }} characters long',
                    'groups' => 'referee_details',
                ))
            )
        ));
        
        return $this;
    }

    /**
     * Add the contact name field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addContactName(FormInterface $form)
    {
        $form->add('contactName', 'text', array(
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'referee_details',
                    'message' => 'Please enter contact name',
                )),
                new Constraints\Length(array(
                    'min' => 2,
                    'groups' => 'referee_details',
                    'minMessage' => 'Contact name must be at least {{ limit }} characters long',
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the contact position field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addContactPosition(FormInterface $form)
    {
        $form->add('contactPosition', 'text', array(
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'referee_details',
                    'message' => 'Please enter contact position',
                )),
                new Constraints\Length(array(
                    'min' => 2,
                    'groups' => 'referee_details',
                    'minMessage' => 'Contact position must be at least {{ limit }} characters long',
                ))
            )
        ));

        return $this;
    }

    /**
     * add the phone field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addPhone(FormInterface $form)
    {
        $form->add('phone', 'text', array(
            'label' => 'Telephone (day)',
            // Required field, must be at least 9 chars long
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'referee_details',
                    'message' => 'Please enter a telephone number',
                )),
                new Constraints\Length(array(
                    'min' => 9,
                    'groups' => 'contact_details',
                    'minMessage' => 'Phone number must be at least {{ limit }} characters long',
                )),
                new Constraints\Regex(array(
                    'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                    'message' => 'Phone number is invalid',
                )),

                $this->notApplicantPhoneConstraintSubscriber->getConstraint(),
            )
        ));

        return $this;
    }

    /**
     * Add the fax field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addFax(FormInterface $form)
    {
        $form->add('fax', 'text', array(
            // Not required field, but if supplied must be at least 9 chars long
            'constraints' => array(
                new Constraints\Length(array(
                    'min' => 9,
                    'groups' => 'contact_details',
                    'minMessage' => 'Fax number must be at least {{ limit }} characters long',
                )),
                new Constraints\Regex(array(
                    'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                    'message' => 'Fax number is invalid',
                )),
            ),
            'required' => false,
        ));

        return $this;
    }

    /**
     * Add the email field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addEmail(FormInterface $form)
    {
        $form->add('email', 'email', array(
            'required' => false,
            'constraints' => array(
                new Constraints\Email(array(
                    'groups' => 'contact_details',
                    'message' => 'Email address is not valid',
                )),

                $this->notApplicantEmailConstraintSubscriber->getConstraint(),
            ),
        ));

        return $this;
    }

    /**
     * Add the address field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addAddress(FormInterface $form)
    {
        $form->add('address', $this->addressType, array(
            'is_international_type' => true,
        ));

        return $this;
    }

    /**
     * Add the applicant position held field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addApplicantPositionHeld(FormInterface $form)
    {
        $form->add('applicantPositionHeld', 'text', array(
            'label' => 'Position Held by Applicant',
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'referee_details',
                    'message' => 'Please enter position held by applicant',
                )),
                new Constraints\Length(array(
                    'min' => 2,
                    'groups' => 'applicant_details',
                    'minMessage' => 'Position held by applicant must be at least {{ limit }} characters long',
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the is position field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addIsPermanent(FormInterface $form)
    {
        $form->add('isPermanent', 'choice', array(
            'choices' => array(
                'true' => 'Yes',
                'false' => 'No',
            ),
            'label' => 'Is the position permanent?',
            'required' => true,
            'expanded' => true,
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'applicant_details',
                    'message' => 'Please select if the position is permanent',
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the payroll number field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addPayrollNumber(FormInterface $form)
    {
        $form->add('payrollNumber', 'text', array(
            'label' => 'Payroll/Service/Pension/National Insurance Number',
        ));

        return $this;
    }

    /**
     * Add the applicant annual income field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addApplicantAnnualIncome(FormInterface $form)
    {
        $form->add('applicantAnnualIncome', 'money', array(
            'label' => 'Gross Annual Salary',
            'currency' => 'GBP',
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'income',
                    'message' => 'Please enter applicant annual income',
                )),
                new Constraints\Regex(array(
                    'groups' => 'income',
                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                    'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the applicant annual earnings field.
     * Note that the field is added under the name applicantAnnualIncome.
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addApplicantAnnualEarnings(FormInterface $form)
    {
        $form->add('applicantAnnualIncome', 'money', array(
            'label' => 'Gross annual earnings',
            'currency' => 'GBP',
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'income',
                    'message' => 'Please enter your gross annual earnings',
                )),
                new Constraints\Regex(array(
                    'groups' => 'income',
                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                    'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"',
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the applicant annual pension field
     * Note that the field is added under the name applicantAnnualIncome.
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addApplicantAnnualPension(FormInterface $form)
    {
        $form->add('applicantAnnualPension', 'money', array(
            'label' => 'Gross annual pension',
            'currency' => 'GBP',
            'constraints' => array(
                new Constraints\NotNull(array(
                    'groups' => 'income',
                    'message' => 'Please enter gross annual pension',
                )),
                new Constraints\Regex(array(
                    'groups' => 'income',
                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                    'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"',
                ))
            )
        ));

        return $this;
    }

    /**
     * Add the employment start date field
     *
     * @param FormInterface $form
     * @return $this
     */
    public function addEmploymentStartDate(FormInterface $form)
    {
        $form->add('employmentStartDate', 'date', array(
            'label' => 'Start Date',
            'years' => range((int) date('Y') + 2, (int) date('Y') - 100),
            'placeholder' => '--',
            'constraints' => array(
                new Constraints\NotBlank(),
                new Constraints\Date(array(
                    'groups' => 'applicant_position_commencement',
                    'message' => 'Start date invalid',
                ))
            ),
            'attr' => array(
                'data-provide' => 'datepicker',
                'data-end-date' => date('d/m/Y')
            ),
        ));
        
        return $this;
    }
}
