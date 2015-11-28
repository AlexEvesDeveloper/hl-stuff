<?php

namespace RRP\Form;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use Iris\IndividualApplication\Search\IndividualApplicationSearch;
use RRP\Common\Countries;
use RRP\Common\Titles;
use RRP\Common\ReferenceTypes;
use RRP\Common\TenancyAgreementTypes;
use RRP\Common\PropertyLetTypes;
use RRP\DependencyInjection\LegacyContainer;
use RRP\Form\DataTransformer\ReferenceToProductTypeTransformer;
use RRP\Form\Subscriber\VerifyReferenceSubscriber;
use RRP\Model\RentRecoveryPlusApplication;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Class RentRecoveryPlusApplicationType
 *
 * @package RRP\Form
 * @author April Portus <april.portus@barbon.com>
 * @author Alex Eves <alex.eves@barbon.com>
 */
class RentRecoveryPlusApplicationType extends AbstractType
{
    /**
     * @var VerifyReferenceSubscriber
     */
    protected $verifyReferenceSubscriber;

    /**
     * @var DataTransformerInterface
     */
    protected $referenceNumberTransformer;

    /**
     * @var DataTransformerInterface
     */
    protected $referenceTypeTransformer;

    /**
     * RentRecoveryPlusApplication constructor
     *
     * @param VerifyReferenceSubscriber $verifyReferenceSubscriber
     * @param DataTransformerInterface $referenceNumberTransformer
     * @param DataTransformerInterface $referenceTypeTransformer
     */
    public function __construct(
        VerifyReferenceSubscriber $verifyReferenceSubscriber,
        DataTransformerInterface $referenceNumberTransformer,
        DataTransformerInterface $referenceTypeTransformer
    ) {
        $this->verifyReferenceSubscriber = $verifyReferenceSubscriber;
        $this->referenceNumberTransformer = $referenceNumberTransformer;
        $this->referenceTypeTransformer = $referenceTypeTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultUpdateList = array(
            'referenceType:*',
            'isContinuationOfExistingPolicy:1',
            'landlordTitle:Other',
            'hasPossibleClaimCircumstances:1',
            'propertyLetType:Let Only',
            'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE
        );
        if (array_key_exists('data', $options)) {
            $builder->add('updateList', 'hidden');
        }
        else {
            $builder->add('updateList', 'hidden', array('data' => implode(',', $defaultUpdateList)));
        }

        $this->referenceTypeTransformer->setCurrentAsn($options['currentAsn']);

        $builder
            // Product
            ->add('policyLength', 'choice', array(
                'choices' => array(
                    6 => '6 Months',
                    12 => '12 Months',
                ),
                'label' => 'Policy Term',
                'expanded' => true,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add(
                $builder->create('referenceType', 'choice', array(
                    'choices' => ReferenceTypes::getReferenceTypes(),
                    'empty_value' => false,
                    'expanded' => true,
                ))->addModelTransformer($this->referenceTypeTransformer)
            )
            ->add('isNilExcess', 'choice', array(
                'choices' => array(
                    1 => 'Nil Excess',
                    0 => '1 Month Excess',
                ),
                'label' => 'Excess',
                'empty_value' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('isContinuationOfExistingPolicy', 'choice', array(
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Will this policy be a continuation of cover from a previous policy?',
                'empty_value' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            // Property
            ->add('propertyLetType', 'choice', array(
                'choices' => PropertyLetTypes::getPropertyLetTypes(),
                'empty_value' => '- Please Select -',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('propertyAddress1', 'text', array(
                'label' => 'Property house number + street',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('propertyAddress2', 'text', array(
                'label' => 'Property town/city',
                'required' => false,
            ))
            ->add('propertyPostcode', 'text', array(
                'attr' => array(
                    'class' => 'first',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please enter postcode',
                        'groups' => 'fullValidation',
                    )),
                ),
            ))
            ->add('propertyRental', 'money', array(
                'label' => 'Monthly rental amount',
                'required' => true,
                'constraints' => array(
                    new Assert\GreaterThan(array(
                        'value' => 0,
                        'message' => 'Please enter a rental amount greater than £0',
                        'groups' => 'fullValidation',
                    )),
                ),
            ))
            ->add('propertyDeposit', 'money', array(
                'label' => 'Deposit amount',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    )),
                ),
            ))
            ->add('hasPaidRentInAdvance', 'choice', array(
                'choices' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'label' => 'Has the first month’s rent been paid in advance?',
                'empty_value' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('tenancyStartAt', 'date', array(
                'label' => 'Tenancy start date (dd/mm/yyyy)',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-provide' => 'datepicker',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('policyStartAt', 'date', array(
                'label' => 'Policy start date (dd/mm/yyyy)',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-provide' => 'datepicker',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('policyEndAt', 'text', array(
                'label' => 'Policy end date (dd/mm/yyyy)',
                'attr' => array(
                    'readonly' => 'readonly',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
                'mapped' => false
            ))
            // Landlord
            ->add('landlordTitle', 'choice', array(
                'label' => 'Title',
                'attr' => array(
                    'class' => 'first',
                ),
                'choices' => Titles::getTitles(),
                'empty_value' => false,
                'required' => true,
            ))
            ->add('landlordFirstName', 'text', array(
                'label' => 'First name',
                'attr' => array(
                    'class' => 'first',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('landlordLastName', 'text', array(
                'label' => 'Last name',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('landlordEmailAddress', 'repeated', array(
                'type' => 'email',
                'first_name' => 'emailAddress',
                'second_name' => 'confirmEmailAddress',
                'required' => false,
                'invalid_message' => 'Email addresses do not match'
            ))
            ->add('landlordPhoneNumber', 'text', array(
                'label' => 'Phone number',
                'required' => false,
            ))
            ->add('landlordAddress1', 'text', array(
                'label' => 'Landlord house number + street',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('landlordAddress2', 'text', array(
                'label' => 'Landlord town/city',
                'required' => true,
            ))
            ->add('landlordPostcode', 'text', array(
                'attr' => array(
                    'class' => 'first',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please enter postcode',
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('landlordCountry', 'choice', array(
                'choices' => Countries::getCountries(),
                'data' => Countries::GB,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('isPayMonthly', 'choice', array(
                'label' => 'Payment type',
                'required' => true,
                'choices' => array(
                    0 => 'Annually',
                    1 => 'Monthly',
                ),
                'empty_value' => false,
                'expanded' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->add('hasPossibleClaimCircumstances', 'choice', array(
                'required' => true,
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Are you aware of any circumstances which may give rise to a claim?',
                'empty_value' => false,
                'expanded' => true,
            ))
            ->add('hasPermittedOccupiersOnly', 'choice', array(
                'required' => true,
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Will only Tenants or permitted occupiers be living at the property?',
                'empty_value' => false,
                'expanded' => true,
            ))
            ->add('hasTenancyDisputes', 'choice', array(
                'required' => true,
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Any tenancy disputes, including late payment of rent or rental arrears?',
                'empty_value' => false,
                'expanded' => true,
            ))
            ->add('hasPriorClaims', 'choice', array(
                'required' => true,
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Have there been any claims logged during the existing tenancy?',
                'empty_value' => false,
                'expanded' => true,
            ))
            ->add('isDepositSufficient', 'choice', array(
                'required' => true,
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'label' => 'Was a deposit with a sum equivalent to (or greater than) 1 months rent taken prior to the commencement of the tenancy?',
                'empty_value' => false,
                'expanded' => true,
            ))
            ->add('tenancyAgreementType', 'choice', array(
                'label' => 'Type of tenancy agreement',
                'required' => true,
                'choices' => TenancyAgreementTypes::getTenancyAgreementTypes(),
                'empty_value' => false,
                'expanded' => true,
            ))
            // Declaration
            ->add('confirmation1', 'checkbox', array(
                'required' => true,
                'value' => 1,
                'label' => 'I confirm that the statements above are true to the best of my knowledge',
                'mapped' => 0
            ))
            ->add('confirmation2', 'checkbox', array(
                'required' => true,
                'value' => 1,
                'label' => 'I confirm that I have read both the IDD and policy summary',
                'mapped' => 0
            ))
            //
            ->add('update', 'submit', array(
                'attr' => array(
                    'value' => '1',
                ),
            ))
            ->add('isXhr', 'hidden', array(
                'attr' => array(
                    'value' => '0',
                ),
            ))
            ->add('policyNumber', 'hidden')
            ->setMethod('GET')
        ;


        /**
         * Form modifier for optional fields
         *
         * @param FormEvent $event
         */
        $verifyReferenceSubscriber = $this->verifyReferenceSubscriber;
        $verifyReferenceSubscriber->setCurrentAsn($options['currentAsn']);
        $referenceNumberTransformer = $this->referenceNumberTransformer;
        $referenceNumberTransformer->setCurrentAsn($options['currentAsn']);

        $optionsFormModifier = function (FormEvent $event) use ($verifyReferenceSubscriber, $referenceNumberTransformer)
        {
            $form = $event->getForm();
            $data = $event->getData();

            $updateList = array();

            if (null !== $data) {

                if (is_array($data)) {
                    /** @var RentRecoveryPlusApplication $application */
                    $application = RentRecoveryPlusApplication::hydrate($data);
                }
                else if ($data instanceof RentRecoveryPlusApplication) {
                    $application = $data;
                }
                else {
                    return;
                }
                $updateData = false;

                $updateList[] = 'referenceType:*';
                if ($application->getReferenceType()) {
                    if (ReferenceTypes::isProviderRequired($application->getReferenceType())) {
                        $form
                            ->add('otherProvider', 'text', array(
                                'required' => true,
                                'constraints' => array(
                                    new Assert\NotBlank(array(
                                        'groups' => 'fullValidation',
                                    ))
                                ),
                            ));
                        if ($form->has('referenceNumber')) {
                            $form->remove('referenceNumber');
                        }
                        if (array_key_exists('referenceNumber', $data)) {
                            unset($data['referenceNumber']);
                            $updateData = true;
                        }
                    } else {
                        $updateList[] = 'referenceNumber:*';

                        $form->add('references', 'collection', array(
                            'type' => new EventAwareTextType(array(
                                'subscribers' => $verifyReferenceSubscriber,
                                'view_transformers' => $referenceNumberTransformer
                            )),
                            'allow_add' => true,
                            'options' => array(
                                'label' => 'Reference number',
                                'required' => false
                            )
                        ));

                        if ($form->has('otherProvider')) {
                            $form->remove('otherProvider');
                        }

                        if (array_key_exists('otherProvider', $data)) {
                            unset($data['otherProvider']);
                            $updateData = true;
                        }
                    }
                }

                if (ReferenceTypes::isNilExcessAllowed($application->getReferenceType())) {
                    $form->remove('isNilExcess');
                    $form
                        ->add('isNilExcess', 'choice', array(
                            'required' => true,
                            'choices' => array(
                                1 => 'Nil Excess',
                                0 => '1 Month Excess',
                            ),
                            'label' => 'Excess',
                            'empty_value' => false,
                            'expanded' => true,
                        ));
                }
                else {
                    $form->remove('isNilExcess');
                    $form
                        ->add('isNilExcess', 'choice', array(
                            'choices' => array(
                                0 => '1 Month Excess',
                            ),
                            'label' => 'Excess',
                            'empty_value' => false,
                            'expanded' => true,
                        ));
                    if ($application->getIsNilExcess()) {
                        $form->get('isNilExcess')->setData(0);
                        $data['isNilExcess'] = 0;
                        $application->setIsNilExcess(0);
                        $updateData = true;
                    }
                }

                /* Phase 2
                if (
                    $application->getIsNilExcess() &&
                    ! $application->getPropertyDeposit() &&
                    $application->getPropertyDeposit() <= 0.0
                ) {
                    $form->add('hasNilDepositInsurance', 'checkbox', array(
                        'required' => true,
                        'value' => 1,
                        'label' => 'please confirm that you have a nil deposit insurance or warranty in place'
                            . ' which extends to the equivalent of one month’s rent',
                    ));
                }
                else {
                    if ($form->has('hasNilDepositInsurance')) {
                        $form->remove('hasNilDepositInsurance');
                    }
                    if (array_key_exists('hasNilDepositInsurance', $data)) {
                        unset($data['hasNilDepositInsurance']);
                        $updateData = true;
                    }
                }
                if ($application->getHasPaidRentInAdvance()) {
                    $form->add('numberMonthsRentInAdvance', 'decimal', array(
                        'required' => true,
                        'constraints' => array(
                            new Assert\Length(array(
                                'min' => 1,
                                'groups' => 'fullValidation',
                            ))
                        ),
                    ));
                }
                else {
                    if ($form->has('numberMonthsRentInAdvance')) {
                        $form->remove('numberMonthsRentInAdvance');
                    }
                    if (array_key_exists('numberMonthsRentInAdvance', $data)) {
                        unset($data['numberMonthsRentInAdvance']);
                        $updateData = true;
                    }
                }
                */

                if ($application->getIsContinuationOfExistingPolicy()) {
                    $updateList[] = 'isContinuationOfExistingPolicy:0';
                    $form
                        ->add('existingPolicyRef', 'text', array(
                            'required' => true,
                            'constraints' => array(
                                new Assert\NotBlank(array(
                                    'groups' => 'fullValidation',
                                ))
                            ),
                        ))
                        ->add('isExistingPolicyToBeCancelled', 'choice', array(
                            'required' => true,
                            'choices' => array(
                                1 => 'Yes',
                                0 => 'No',
                            ),
                            'label' => 'Do you wish us to cancel this RG policy?',
                            'empty_value' => false,
                            'expanded' => true,
                        ))
                    ;
                    if (
                        ($application->getIsExistingPolicyToBeCancelled() === null) ||
                        ($application->getIsExistingPolicyToBeCancelled())
                    ) {
                        $updateList[] = 'isExistingPolicyToBeCancelled:0';
                        if ($form->has('cancellationObjections')) {
                            $form->remove('cancellationObjections');
                        }
                        if (array_key_exists('cancellationObjections', $data)) {
                            unset($data['cancellationObjections']);
                            $updateData = true;
                        }
                    }
                    else {
                        $updateList[] = 'isExistingPolicyToBeCancelled:1';
                        $form
                            ->add('cancellationObjections', 'textarea', array(
                                'required' => true,
                            ));
                    }
                }
                else {
                    $updateList[] = 'isContinuationOfExistingPolicy:1';
                    if ($form->has('existingPolicyRef')) {
                        $form->remove('existingPolicyRef');
                    }
                    if (array_key_exists('existingPolicyRef', $data)) {
                        unset($data['existingPolicyRef']);
                        $updateData = true;
                    }
                    if ($form->has('isExistingPolicyToBeCancelled')) {
                        $form->remove('isExistingPolicyToBeCancelled');
                    }
                    if (array_key_exists('isExistingPolicyToBeCancelled', $data)) {
                        unset($data['isExistingPolicyToBeCancelled']);
                        $updateData = true;
                    }
                    if ($form->has('cancellationObjections')) {
                        $form->remove('cancellationObjections');
                    }
                    if (array_key_exists('cancellationObjections', $data)) {
                        unset($data['cancellationObjections']);
                        $updateData = true;
                    }
                }

                if ($application->getLandlordTitle() == 'Other') {
                    $updateList[] = 'landlordTitle:*';
                    $form
                        ->add('landlordOtherTitle', 'text', array(
                            'label' => 'Please specify title',
                            'required' => true,
                            'constraints' => array(
                                new Assert\Length(array(
                                    'min' => 2,
                                    'groups' => 'fullValidation',
                                ))
                            ),
                        ));
                }
                else {
                    $updateList[] = 'landlordTitle:Other';
                    if ($form->has('landlordOtherTitle')) {
                        $form->remove('landlordOtherTitle');
                    }
                    if (array_key_exists('landlordOther', $data)) {
                        unset($data['landlordOtherTitle']);
                        $updateData = true;
                    }
                }

                if ($application->getHasPossibleClaimCircumstances()) {
                    $updateList[] = 'hasPossibleClaimCircumstances:0';
                    $form
                        ->add('claimInfo', 'textarea', array(
                            'required' => true,
                            'constraints' => array(
                                new Assert\NotBlank(array(
                                    'groups' => 'fullValidation',
                                ))
                            ),
                        ));
                }
                else {
                    $updateList[] = 'hasPossibleClaimCircumstances:1';
                    if ($form->has('claimInfo')) {
                        $form->remove('claimInfo');
                    }
                    if (array_key_exists('claimInfo', $data)) {
                        unset($data['claimInfo']);
                        $updateData = true;
                    }
                }

                if (PropertyLetTypes::isLandlordPermissionRequired($application->getPropertyLetType())) {
                    $updateList[] = 'propertyLetType:*';
                    $form
                        ->add('hasLandlordPermission', 'choice', array(
                            'required' => true,
                            'choices' => array(
                                1 => 'Yes',
                                0 => 'No',
                            ),
                            'label' => 'Where a Let Only agreement is in place you may give your Landlord permission to report and progress a claim on your behalf. Please tick this box to confirm if you have granted this permission, and that your Landlord has been made aware of, and has confirmed understanding of, the policy conditions around claiming',
                            'empty_value' => false,
                            'expanded' => true,
                        ));
                }
                else {
                    $updateList[] = 'propertyLetType:Let Only';
                    if ($form->has('hasLandlordPermission')) {
                        $form->remove('hasLandlordPermission');
                    }
                    if (array_key_exists('hasLandlordPermission', $data)) {
                        unset($data['hasLandlordPermission']);
                        $updateData = true;
                    }
                }

                if ( ! $application->getTenancyAgreementType()) {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE;
                }
                else if (TenancyAgreementTypes::isAssuredShortholdTenancy($application->getTenancyAgreementType())) {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE;
                }
                else {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::ASSURED_SHORTHOLD_TENANCY_TYPE;
                }

                $updateListString = implode(',', $updateList);
                if ($application->getUpdateList() != $updateListString) {
                    $updateData = true;
                    if (is_array($data)) {
                        $data['updateList'] = $updateListString;
                    }
                    else {
                        $application->setUpdateList($updateListString);
                    }
                }

                if ($updateData) {
                    if (is_array($data)) {
                        $event->setData($data);
                    }
                    else {
                        $event->setData($application);
                    }
                }
            }
        };


        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($optionsFormModifier) {
                $optionsFormModifier($event);
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($optionsFormModifier) {
                $optionsFormModifier($event);
            })
        ;
    }

    /**
     * Validation function for optional constraints
     *
     * @param RentRecoveryPlusApplication $application
     * @param ExecutionContextInterface $context
     */
    public function checkRateBandLimits($application, ExecutionContextInterface $context)
    {
        if (
            ReferenceTypes::isOnlyAllowBandA($application->getReferenceType()) &&
            $application->getPropertyRental() > RentRecoveryPlus::BAND_A_LIMIT
        ) {
            $context->addViolationAt(
                'propertyRental',
                'Monthly rental amount cannot be greater than £' . RentRecoveryPlus::BAND_A_LIMIT . ' for the selected reference type',
                array(),
                null
            );
        }
        else if ($application->getPropertyRental() > RentRecoveryPlus::BAND_B_LIMIT) {
            $context->addViolationAt(
                'propertyRental',
                'Monthly rental amount cannot be greater than £' . RentRecoveryPlus::BAND_B_LIMIT,
                array(),
                null
            );
        }
    }

    /**
     * Validation function for optional constraints
     *
     * @param RentRecoveryPlusApplication $application
     * @param ExecutionContextInterface $context
     */
    public function checkPostcodes($application, ExecutionContextInterface $context)
    {
        $container = new LegacyContainer();
        $postcodeFields = array('propertyPostcode', 'landlordPostcode');
        foreach ($postcodeFields as $fieldName) {
            $getter = sprintf( 'get%s', ucfirst($fieldName) );
            $submittedPostcode = $application->{$getter}();
            if ('' != $submittedPostcode) {
                /** @var \Application_Core_Postcode $validatorClass */
                $validatorClass = $container->get('rrp.legacy.postcode_validator.class');
                $newPostcode = $validatorClass::validate($submittedPostcode);
                if ('' == $newPostcode) {
                    $context->addViolationAt(
                        $fieldName,
                        'Postcode invalid, must be e.g. NE63 9UD',
                        array(),
                        null
                    );
                } else {
                    $setter = sprintf('set%s', ucfirst($fieldName));
                    $application->{$setter}($newPostcode);
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'currentAsn' => null,
            'data_class' => 'RRP\Model\RentRecoveryPlusApplication',
            'constraints' =>
                array(
                    new Assert\Callback(
                        array('methods' =>
                            array(
                                array($this, 'checkRateBandLimits'),
                                array($this, 'checkPostcodes'),
                            )
                        )
                    )
                ),
            'validation_groups' => function (FormInterface $form)
            {
                $update = false;

                if ($form->has('update')) {
                    $update = $form->get('update')->isclicked();
                }

                if ( ! $update) {
                    if ($form->has('isXhr')) {
                        /** @var RentRecoveryPlusApplication $application */
                        $application = $form->getData();
                        if ($application instanceof RentRecoveryPlusApplication) {
                            $update = $application->getIsXhr();
                        }
                    }
                }

                if ($update) {
                    return array('Default', 'referenceValidation');
                }
                else {
                    return array('Default', 'fullValidation');
                }
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rent_recovery_plus_application';
    }
}