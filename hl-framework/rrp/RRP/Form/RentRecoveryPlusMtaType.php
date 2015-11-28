<?php

namespace RRP\Form;

use RRP\Common\ReferenceTypes;
use RRP\Common\TenancyAgreementTypes;
use RRP\Model\RentRecoveryPlusMta;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Class RentRecoveryPlusMtaType
 *
 * @package RRP\Form
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusMtaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultUpdateList = array(
            'hasPossibleClaimCircumstances:1',
            'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE
        );
            $builder->add('updateList', 'hidden', array('data' => implode(',', $defaultUpdateList)));

        $builder
            ->add('policyNumber', 'hidden')
            ->add('policyExpiresAt', 'hidden')
            ->add('referenceType', 'hidden')
            // Property
            ->add('propertyRental', 'money', array(
                'label' => 'Monthly Rental Amount',
                'required' => true,
                'attr' => array(
                    'class' => 'rrpi-input-group-right',
                ),
                'constraints' => array(
                    new Assert\GreaterThan(array(
                        'value' => 0,
                        'message' => 'Please enter a rental amount greater than £0',
                        'groups' => 'fullValidation',
                    )),
                ),
            ))
            ->add('mtaEffectiveAt', 'date', array(
                'label' => 'MTA Effective Date (dd/mm/yyyy)',
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
            // Landlord
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
            ->setMethod('GET')
        ;

        /**
         * Form modifier for optional fields
         *
         * @param FormEvent $event
         */
        $optionsFormModifier = function (FormEvent $event)
        {
            $form = $event->getForm();
            $data = $event->getData();

            $updateList = array();

            if (null !== $data) {
                /** @var RentRecoveryPlusMta $mta */
                $mta = RentRecoveryPlusMta::hydrate($data);
                $updateData = false;

                if ($mta->getHasPossibleClaimCircumstances()) {
                    $updateList[] = 'hasPossibleClaimCircumstances:0';
                    $form
                        ->add('claimInfo', 'textarea', array(
                            'required' => true,
                            'constraints' => array(
                                new Assert\Length(array(
                                    'min' => 2,
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

                if ( ! $mta->getTenancyAgreementType()) {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE;
                }
                else if (TenancyAgreementTypes::isAssuredShortholdTenancy($mta->getTenancyAgreementType())) {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::COMPANY_TENANCY_TYPE;
                }
                else {
                    $updateList[] = 'tenancyAgreementType:' . TenancyAgreementTypes::ASSURED_SHORTHOLD_TENANCY_TYPE;
                }

                $updateListString = implode(',', $updateList);
                if ($mta->getUpdateList() != $updateListString) {
                    $updateData = true;
                    $data['updateList'] = $updateListString;
                }

                if ($updateData) {
                    $event->setData($data);
                }
            }
        };


        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($optionsFormModifier) {
                $optionsFormModifier($event);
            })
        ;
    }

    /**
     * Validation function for optional constraints
     *
     * @param RentRecoveryPlusMta $mta
     * @param ExecutionContextInterface $context
     */
    public function checkRateBandLimits($mta, ExecutionContextInterface $context)
    {
        if (
            ReferenceTypes::isOnlyAllowBandA($mta->getReferenceType()) &&
            $mta->getPropertyRental() > RentRecoveryPlus::BAND_A_LIMIT
        ) {
            $context->addViolationAt(
                'propertyRental',
                'Monthly rental amount cannot be greater than £' . RentRecoveryPlus::BAND_A_LIMIT,
                array(),
                null
            );
        }
        else if ($mta->getPropertyRental() > RentRecoveryPlus::BAND_B_LIMIT) {
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
     * @param RentRecoveryPlusMta $mta
     * @param ExecutionContextInterface $context
     */
    public function checkDates($mta, ExecutionContextInterface $context)
    {
        if ('0000-00-00' === $mta->getMtaEffectiveAt()) {
            return;
        }

        $startAt = new \DateTime($mta->getPolicyStartedAt());
        $expiresAt = new \DateTime($mta->getPolicyExpiresAt());
        $effectiveAt = new \DateTime($mta->getMtaEffectiveAt());
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        if ($effectiveAt > $expiresAt) {
            $context->addViolationAt(
                'mtaEffectiveAt',
                'Requested date exceeds existing end date of ' . $expiresAt->format('d/m/Y'),
                array(),
                null
            );
        }
        else if ($effectiveAt < $now) {
            $context->addViolationAt(
                'mtaEffectiveAt',
                'Requested date is in the past',
                array(),
                null
            );
        }
        else if ($effectiveAt < $startAt) {
            $context->addViolationAt(
                'mtaEffectiveAt',
                'Requested date is prior to policy start date of ' . $startAt->format('d/m/Y'),
                array(),
                null
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'data_class' => 'RRP\Model\RentRecoveryPlusMta',
            'constraints' =>
                array(
                    new Assert\Callback(
                        array('methods' =>
                            array(
                                array($this, 'checkRateBandLimits'),
                                array($this, 'checkDates'),
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
                            /** @var RentRecoveryPlusMta $mta */
                            $mta = $form->getData();
                            if ($mta instanceof RentRecoveryPlusMta) {
                                $update = $mta->getIsXhr();
                            }
                        }
                    }

                    if ($update) {
                        return array('Default');
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
        return 'rent_recovery_plus_mta';
    }
}