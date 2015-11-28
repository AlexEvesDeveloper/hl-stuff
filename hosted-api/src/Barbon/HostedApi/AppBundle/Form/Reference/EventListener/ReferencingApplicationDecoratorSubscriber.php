<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Common\DataTransformer\StringToBooleanChoiceTransformer;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\CompletionMethod;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\EmploymentStatus;
use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\Product;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\AddressHistoryDurationCalculationSubscriber;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\AddressHistoryReorderSubscriber;
use Barbon\HostedApi\AppBundle\Form\Common\Extension\Type\EventAwareCollectionType;
use Barbon\HostedApi\AppBundle\Form\Common\Extension\Type\EventAwareTextType;
use Barbon\HostedApi\AppBundle\Form\Common\Extension\Type\EventAwareChoiceType;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\AddressHistoryDuration;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\FinancialRefereeCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints;

class ReferencingApplicationDecoratorSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormTypeInterface
     */
    private $bankAccountType;
    
    /**
     * @var ChoiceListInterface
     */
    private $residentialStatusLookup;

    /**
     * @var ChoiceListInterface
     */
    private $employmentStatusLookup;

    /**
     * @var FormTypeInterface
     */
    private $financialRefereeType;

    /**
     * @var FormTypeInterface
     */
    private $lettingRefereeType;

    /**
     * @var FormTypeInterface
     */
    private $previousAddressType;

    /**
     * @var FormTypeInterface
     */
    private $guarantorType;

    /**
     * @var EventSubscriberInterface
     */
    private $residentialStatusLettingRefereeSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $notApplicantPhoneBridgeSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $financialRefereeStatusDesignationSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $guarantorDecoratorBridgeSubscriber;

    /**
     * Constructor
     *
     * @param FormTypeInterface $bankAccountType
     * @param FormTypeInterface $financialRefereeType
     * @param FormTypeInterface $lettingRefereeType
     * @param FormTypeInterface $previousAddressType
     * @param FormTypeInterface $guarantorType
     * @param EventSubscriberInterface $residentialStatusLettingRefereeSubscriber
     * @param EventSubscriberInterface $notApplicantPhoneBridgeSubscriber
     * @param EventSubscriberInterface $financialRefereeStatusDesignationSubscriber
     * @param EventSubscriberInterface $guarantorDecoratorBridgeSubscriber
     * @param ChoiceListInterface $residentialStatusLookup
     * @param ChoiceListInterface $employmentStatusLookup
     */
    public function __construct(
        FormTypeInterface $bankAccountType,
        FormTypeInterface $financialRefereeType,
        FormTypeInterface $lettingRefereeType,
        FormTypeInterface $previousAddressType,
        FormTypeInterface $guarantorType,
        EventSubscriberInterface $residentialStatusLettingRefereeSubscriber,
        EventSubscriberInterface $notApplicantPhoneBridgeSubscriber,
        EventSubscriberInterface $financialRefereeStatusDesignationSubscriber,
        EventSubscriberInterface $guarantorDecoratorBridgeSubscriber,
        ChoiceListInterface $residentialStatusLookup,
        ChoiceListInterface $employmentStatusLookup
    )
    {
        // Child form builders
        $this->bankAccountType = $bankAccountType;
        $this->financialRefereeType = $financialRefereeType;
        $this->lettingRefereeType = $lettingRefereeType;
        $this->previousAddressType = $previousAddressType;
        $this->guarantorType = $guarantorType;

        // Event subscribers
        $this->residentialStatusLettingRefereeSubscriber = $residentialStatusLettingRefereeSubscriber;
        $this->notApplicantPhoneBridgeSubscriber = $notApplicantPhoneBridgeSubscriber;
        $this->financialRefereeStatusDesignationSubscriber = $financialRefereeStatusDesignationSubscriber;
        $this->guarantorDecoratorBridgeSubscriber = $guarantorDecoratorBridgeSubscriber;

        $this->residentialStatusLookup = $residentialStatusLookup;
        $this->employmentStatusLookup = $employmentStatusLookup;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * PRE_SET_DATA handler
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ReferencingApplication $application */
        $application = $event->getData();
        $productId = null;
        $completionMethod = null;
        $employmentStatus = null;

        if ($application instanceof ReferencingApplication) {
            $productId = $application->getProductId();
            $completionMethod = $application->getCompletionMethod();
            $employmentStatus = $application->getEmploymentStatus();
        }

        $form = $event->getForm();
        $this->decorateForm($productId, $completionMethod, $employmentStatus, $form);
    }

    /**
     * PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $application = $event->getData();
        $productId = null;
        $completionMethod = null;
        $employmentStatus = null;

        if (is_array($application)) {
            if (isset($application['productId'])) {
                $productId = $application['productId'];
            }

            if (isset($application['completionMethod'])) {
                $completionMethod = $application['completionMethod'];
            }

            if (isset($application['employmentStatus'])) {
                $employmentStatus = $application['employmentStatus'];
            }
        }

        $form = $event->getForm();

        $form
            ->remove('residentialStatus')
            ->remove('employmentStatus')
            ->remove('grossIncome')
            ->remove('bankAccount')
            ->remove('phone')
            ->remove('mobile')
            ->remove('hasCCJ')
            ->remove('addressHistories')
            ->remove('financialReferees')
            ->remove('lettingReferee')
            ->remove('guarantors')
        ;

        $this->decorateForm($productId, $completionMethod, $employmentStatus, $form);
    }

    /**
     * Decorate the form
     *
     * @param $productId
     * @param $completionMethod
     * @param $employmentStatus
     * @param FormInterface $form
     */
    private function decorateForm($productId, $completionMethod, $employmentStatus, FormInterface $form)
    {
        if (CompletionMethod::COMPLETE_NOW == $completionMethod) {
            $this
                ->addResidentialStatus($form)
                ->addEmploymentStatus($form)
                ->addBankAccount($form)
                ->addPhone($form)
                ->addMobile($form)
                ->addHasCcj($form)
                ->addAddressHistory($form)
            ;

            // For Enhance and Optimum only: decide the minimum number of financial referees that must be entered
            // depending on the applicant's employment status.
            $minFinancialReferees = 0;

            if (Product::ENHANCE == $productId || Product::OPTIMUM == $productId) {
                if (
                    EmploymentStatus::EMPLOYED == $employmentStatus ||
                    EmploymentStatus::SELF_EMPLOYED == $employmentStatus ||
                    EmploymentStatus::RETIRED == $employmentStatus ||
                    EmploymentStatus::ON_CONTRACT == $employmentStatus ||
                    EmploymentStatus::INDEPENDENT_MEANS == $employmentStatus
                ) {
                    $minFinancialReferees = 1;
                }
            }

            // Only include extra checks on relevant products
            if (Product::INSIGHT != $productId && Product::XPRESS_RENT_GUARANTEE != $productId) {
                $this
                    ->addFinancialReferees($form, $minFinancialReferees)
                    ->addLettingReferee($form)
                ;
            }

            // Only required for non credit check only products
            if (Product::INSIGHT != $productId) {
                $this
                    ->addGrossIncome($form)
                    ->addGuarantors($form)
                ;
            }
        }
    }

    /**
     * Add the residential status field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addResidentialStatus(FormInterface $form)
    {
        $form->add('residentialStatus', new EventAwareChoiceType(array(
                'subscribers' => $this->residentialStatusLettingRefereeSubscriber,
            )), array(
                'label' => 'Current Residential Status',
                'attr' => array(
                    'class' => 'form-refresh'
                ),
                'choice_list' => $this->residentialStatusLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please select a current residential status',
                    )),
                )
            )
        );

        return $this;
    }

    /**
     * Add the employment status field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addEmploymentStatus(FormInterface $form)
    {
        $form->add('employmentStatus', 'choice', array(
            'label' => 'Current Employment Status',
            'choice_list' => $this->employmentStatusLookup,
            'empty_value' => '- Please Select -',
            'constraints' => array(
                new Constraints\NotBlank(array(
                    'message' => 'Please select a current employment status',
                )),
            ),
        ));

        return $this;
    }

    /**
     * Add the gross income field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addGrossIncome(FormInterface $form)
    {
        $form->add('grossIncome', 'money', array(
            'label' => 'Total Gross Annual Income',
            'currency' => 'GBP',
            'constraints' => array(
                new Constraints\NotBlank(array(
                    'message' => 'Please enter a numeric value of 0 or greater',
                )),
                new Constraints\GreaterThanOrEqual(array(
                    'value' => 0,
                    'message' => 'Please enter a numeric value of 0 or greater',
                )),
                new Constraints\Regex(array(
                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                    'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                )),
            )
        ));

        return $this;
    }

    /**
     * Add the bank account field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addBankAccount(FormInterface $form)
    {
        $form->add('bankAccount', $this->bankAccountType);
        return $this;
    }

    /**
     * Add the phone field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addPhone(FormInterface $form)
    {
        $form->add('phone', new EventAwareTextType(array(
                'subscribers' => $this->notApplicantPhoneBridgeSubscriber,
            )), array(
                'label' => 'Telephone Number',
                'required' => false,
                'constraints' => array(
                    new Constraints\Length(array(
                        'groups' => 'phone',
                        'min' => 9,
                        'minMessage' => 'Please provide at least 9 numeric characters',
                    )),
                    new Constraints\NotBlank(array(
                        'groups' => 'phone',
                        'message' => 'Please provide a telephone number',
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Phone number is invalid',
                    )),
                )
            )
        );

        return $this;
    }

    /**
     * Add the mobile field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addMobile(FormInterface $form)
    {
        $form->add('mobile', 'text', array(
            'label' => 'Mobile Number',
            'required' => false,
            'constraints' => array(
                new Constraints\Length(array(
                    'groups' => 'mobile',
                    'min' => 11,
                    'minMessage' => 'Please provide at least 11 numeric characters',
                )),
                new Constraints\NotBlank(array(
                    'groups' => 'mobile',
                    'message' => 'Please provide a mobile number',
                )),
                new Constraints\Regex(array(
                    'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                    'message' => 'Phone number is invalid',
                )),
            )
        ));
        
        return $this;
    }

    /**
     * Add the has CCJ field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addHasCcj(FormInterface $form)
    {
        $form->add('hasCCJ', new EventAwareChoiceType(array(
                'model_transformers' => new StringToBooleanChoiceTransformer(),
            )), array(
                'choices' => array(
                    'true' => 'Yes',
                    'false' => 'No',
                ),
                'label' => 'Any CCJs or adverse credit history?',
                'required' => true,
                'expanded' => true,
                'constraints' => array(
                    new Constraints\NotNull(),
                ),
            )
        );

        return $this;
    }

    /**
     * add the address history field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addAddressHistory(FormInterface $form)
    {
        $options = array(
            'label' => false,
        );

        $form->add('addressHistories', new EventAwareCollectionType(array(
            'subscribers' => array(
                new AddressHistoryReorderSubscriber($this->previousAddressType, $options),
                new AddressHistoryDurationCalculationSubscriber(),
            ),
        )), array(
            'type' => $this->previousAddressType,
            'prototype_name' => '__previousaddressname__',
            'allow_add' => true,
            'allow_delete' => true,
            'error_bubbling' => false,
            'cascade_validation' => true,
            'options' => $options,
            'constraints' => array(
                new AddressHistoryDuration()
            ),
        ));

        return $this;
    }

    /**
     * Add the financial referees field
     *
     * @param FormInterface $form
     * @param int $minReferees
     * @return $this
     */
    private function addFinancialReferees(FormInterface $form, $minReferees = 0)
    {
        $financialRefereeCollectionConstraint = new FinancialRefereeCollection();
        $financialRefereeCollectionConstraint->minReferees = $minReferees;

        $form->add('financialReferees', new EventAwareCollectionType(array(
                'subscribers' => $this->financialRefereeStatusDesignationSubscriber,
            )), array(
                'type' => $this->financialRefereeType,
                'prototype_name' => '__financialrefereename__',
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'cascade_validation' => true,
                'options' => array(
                    'label' => false,
                ),
                'constraints' => array(
                    $financialRefereeCollectionConstraint,
                ),
            )
        );

        return $this;
    }

    /**
     * Add the letting referee field
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addLettingReferee(FormInterface $form)
    {
        $form->add('lettingReferee', $this->lettingRefereeType);
        return $this;
    }

    /**
     * Add the guarantors collection
     *
     * @param FormInterface $form
     * @return $this
     */
    private function addGuarantors(FormInterface $form)
    {
        $guarantorDecoratorBridgeSubscriber = clone $this->guarantorDecoratorBridgeSubscriber;

        $form->add('guarantors', 'collection', array(
            'type' => $this->guarantorType,
            'prototype_name' => '__guarantorname__',
            'allow_add' => true,
            'allow_delete' => true,
            'error_bubbling' => false,
            'cascade_validation' => true,
            'options' => array(
                'label' => false,
                'guarantor_decorator' => $guarantorDecoratorBridgeSubscriber->getGuarantorDecorator()
            ),
        ));

        return $this;
    }
}
