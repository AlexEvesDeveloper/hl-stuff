<?php

namespace Iris\Referencing\Form\EventListener;

use Barbondev\IRISSDK\Common\Model\FinancialReferee;
use Iris\Referencing\Form\Type\FinancialRefereeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Iris\Referencing\Form\Type\MoneyWithoutStringTransformerType;

/**
 * Class FinancialRefereeDegradationListener
 *
 * @package Iris\Referencing\Form\EventListener
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class FinancialRefereeDegradationListener implements EventSubscriberInterface
{
    const GROSS_ANNUAL_EARNINGS_BLANK_MESSAGE = 'Please enter your gross annual earnings';
    const GROSS_ANNUAL_EARNINGS_INVALID = 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"';

    const GROSS_ANNUAL_PENSION_BLANK_MESSAGE = 'Please enter gross annual pension';
    const GROSS_ANNUAL_PENSION_INVALID = 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"';

    /**
     * POST_SET_DATA event handler
     *
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $financialRefereeStatus = $form->get('financialRefereeStatus')->getData();

        if ($financialRefereeStatus != 1) {

            // Remove non current fields
            $form
                ->remove('employmentChangeDuringTenancy')
                ->remove('multipleJobOrPension')
            ;
        }
    }

    /**
     * PRE_SET_DATA event handler
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var FinancialReferee $financialReferee */
        $financialReferee = $event->getData();
        $data = $event->getData();

        if ($financialReferee instanceof FinancialReferee) {

            $financialRefereeType = $financialReferee->getFinancialRefereeType();

            // If there is no financial referee ID type set, don't build any more of elements of the form.
            if ('' == $financialRefereeType) {
                return;
            }

            switch ($financialRefereeType) {

                // Accountant
                case FinancialRefereeType::ACCOUNTANT:
                    // Remove form fields not needed for this specific employer type and add any that are.
                    $event
                        ->getForm()
                        ->remove('applicantPositionHeld')
                        ->remove('isPermanent')
                        ->remove('payrollNumber')
                        ->add('applicantAnnualIncome', new MoneyWithoutStringTransformerType(), array(
                            'label' => 'Gross annual earnings', // Note label uses "earnings"
                            'currency' => 'GBP', // Must be redefined
                            'constraints' => array(
                                new Assert\NotNull(array(
                                    'groups' => 'income',
                                    'message' => self::GROSS_ANNUAL_EARNINGS_BLANK_MESSAGE,
                                )),
                                new Assert\Regex(array(
                                    'groups' => 'income',
                                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                                    'message' => self::GROSS_ANNUAL_EARNINGS_INVALID
                                ))
                            ))
                        )
                    ;
                    break;

                // Pension Administrator
                case FinancialRefereeType::PENSION_ADMINISTRATOR:
                    // Remove form fields not needed for this specific employer type and add any that are.
                    $event
                        ->getForm()
                        ->remove('applicantPositionHeld')
                        ->remove('isPermanent')
                        ->remove('payrollNumber')
                        ->remove('employmentStartDate')
                        ->add('applicantAnnualIncome', new MoneyWithoutStringTransformerType(), array(
                            'label' => 'Gross annual pension', // Note label uses "pension"
                            'currency' => 'GBP', // Must be redefined
                            'constraints' => array(
                                new Assert\NotNull(array(
                                    'groups' => 'income',
                                    'message' => self::GROSS_ANNUAL_PENSION_BLANK_MESSAGE,
                                )),
                                new Assert\Regex(array(
                                    'groups' => 'income',
                                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                                    'message' => self::GROSS_ANNUAL_PENSION_INVALID
                                ))
                            ))
                        )
                    ;
                    break;

                // Pension statement
                case FinancialRefereeType::PENSION_STATEMENT:
                    // Remove form fields not needed for this specific employer type and add any that are.
                    $event
                        ->getForm()
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
                        ->add('applicantAnnualIncome', new MoneyWithoutStringTransformerType(), array(
                            'label' => 'Gross annual pension', // Note label uses "pension"
                            'currency' => 'GBP', // Must be redefined
                            'constraints' => array(
                                new Assert\NotNull(array(
                                    'groups' => 'income',
                                    'message' => self::GROSS_ANNUAL_PENSION_BLANK_MESSAGE,
                                )),
                                new Assert\Regex(array(
                                    'groups' => 'income',
                                    'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                                    'message' => self::GROSS_ANNUAL_PENSION_INVALID
                                ))
                            ))
                        )
                    ;
                    break;

                // Self Assessment
                case FinancialRefereeType::SELF_ASSESSMENT:
                    // Remove form fields not needed for this specific employer type and add any that are.
                    $event
                        ->getForm()
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
                        ->add('applicantAnnualIncome', new MoneyWithoutStringTransformerType(), array(
                            'label' => 'Gross annual earnings', // Note label uses "pension"
                            'currency' => 'GBP', // Must be redefined
                            'constraints' => array(
                                new Assert\NotNull(array(
                                    'groups' => 'income',
                                    'message' => self::GROSS_ANNUAL_EARNINGS_BLANK_MESSAGE,
                                )),
                                new Assert\Regex(array(
                                        'groups' => 'income',
                                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                                        'message' => self::GROSS_ANNUAL_EARNINGS_INVALID
                                ))
                            ))
                        )
                    ;
                    break;

                // Employer
                case FinancialRefereeType::EMPLOYER:
                default:
                    // No change from default form needed.
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }
}
