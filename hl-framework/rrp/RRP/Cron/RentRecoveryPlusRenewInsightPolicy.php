<?php

namespace RRP\Cron;

use RRP\Application\ApplicationDecoratorFactory;
use RRP\Common\ReferenceTypes;
use RRP\DependencyInjection\LegacyContainer;
use RRP\DependencyInjection\RRPContainer;
use RRP\Rate\RateDecoratorFactory;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Referral\RentRecoveryPlusReferral;
use RRP\Underwriting\Decorators\RentRecoveryPlusAnswers;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class RentRecoveryPlusRenewInsightPolicy
 *
 * @package RRP\Cron
 * @author April Portus <april.portus@barbon.com>
 */
final class RentRecoveryPlusRenewInsightPolicy
{
    /**
     * Identifier that the policy has migrated
     */
    const POLICY_TYPE_MIGRATED = 0;

    /**
     * Identifier that the policy has referred
     */
    const POLICY_TYPE_REFERRED = 1;

    /**
     * Identifier that the policy has the exception flag set so requires further attention
     */
    const POLICY_TYPE_EXCEPTION = 2;

    /**
     * @var LegacyContainer
     */
    private $legacyContainer;

    /**
     * @var RRPContainer
     */
    private $rrpContainer;

    /**
     * @var object
     */
    private $policyNotes;

    /**
     * @var object
     */
    private $rates;

    /**
     * @var array
     */
    private $reportDescriptions;

    /**
     * @var array
     */
    private $reportPolicies;

    /**
     * @var int parameterised constant
     */
    private $customerAddress1;

    /**
     * @var int parameterised constant
     */
    private $customerAddress2;

    /**
     * @var int parameterised constant
     */
    private $customerAddress3;

    /**
     * @var int parameterised constant
     */
    private $customerAgent;

    /**
     * @var int parameterised constant
     */
    private $customerLegacyIdentifier;

    /**
     * @var int parameterised constant
     */
    private $statusPolicy;

    /**
     * @var int parameterised constant
     */
    private $underwritingQuestionSetID;

    /**
     * @var int parameterised constant
     */
    private $riskArea;

    /**
     * @var int parameterised constant
     */
    private $iptPercent;

    /**
     * @var int parameterised constant
     */
    private $payStatusReferred;

    /**
     * @var int parameterised constant
     */
    private $payStatusUpToDate;

    /**
     * Runs the migration action
     */
    public function run()
    {
        $this->legacyContainer = new LegacyContainer();
        $searchClient = $this->legacyContainer->get('rrp.legacy.datasource.search');
        $policyNumberManager = $this->legacyContainer->get('rrp.legacy.manager.policy_number');
        $this->policyNotes = $this->legacyContainer->get('rrp.legacy.datasource.policy_notes');
        $this->rates = $this->legacyContainer->get('rrp.legacy.datasource.rrp_rates');
        $renewalInvitePeriod = $this->legacyContainer->get('rrp.config.renewal_invite_period');
        $insightStatusInsight = $this->legacyContainer->get('rrp.legacy.const.insight_status_insight');
        $insightStatusException = $this->legacyContainer->get('rrp.legacy.const.insight_status_exception');
        $policyOptionRrp = $this->legacyContainer->get('rrp.legacy.const.policy_option_rrp');
        $policyOptionRrpNilExcess = $this->legacyContainer->get('rrp.legacy.const.policy_option_rrp-nilexcess');
        $this->customerAddress1 = $this->legacyContainer->get('rrp.legacy.const.customer_address1');
        $this->customerAddress2 = $this->legacyContainer->get('rrp.legacy.const.customer_address2');
        $this->customerAddress3 = $this->legacyContainer->get('rrp.legacy.const.customer_address3');
        $this->customerAgent = $this->legacyContainer->get('rrp.legacy.const.customer_agent');
        $this->customerLegacyIdentifier = $this->legacyContainer->get('rrp.legacy.const.customer_legacy_identifier');
        $this->statusPolicy = $this->legacyContainer->get('rrp.legacy.const.status_policy');
        $this->underwritingQuestionSetID = $this->legacyContainer->get('rrp.config.underwriting_question_set_id');
        $this->riskArea = $this->legacyContainer->get('rrp.config.risk_area');
        $this->iptPercent = $this->legacyContainer->get('rrp.config.ipt_percent');
        $this->payStatusReferred = $this->legacyContainer->get('rrp.legacy.const.pay_status_referred');
        $this->payStatusUpToDate = $this->legacyContainer->get('rrp.legacy.const.pay_status_up-to-date');

        $this->rrpContainer = new RRPContainer();

        $now = new \DateTime();
        $renewalDate = $now;
        $renewalDate->add(new \DateInterval($renewalInvitePeriod));
        $dateString = $now->format('Y-m-d H:i:s');

        $referral = new RentRecoveryPlusReferral();

        // Initialise the report data
        $this->initialiseReportData();

        // Send assumptive renewals
        $isRenewalInvite = false;
        $policyList = $searchClient->searchForInsightByEndDate($renewalDate, $insightStatusInsight, $isRenewalInvite);

        /** @var \RRP\Application\Decorators\RentRecoveryPlusInsight $insightPolicy */
        $insightPolicy = ApplicationDecoratorFactory::getDecorator('RentRecoveryPlusInsight');

        foreach ($policyList as $rrpPolicyNumber) {

            if ( ! $insightPolicy->populateByPolicyNumber($rrpPolicyNumber)) {
                $this->addToReport(self::POLICY_TYPE_EXCEPTION, $rrpPolicyNumber);
            }
            else {
                $newPolicyNumber = $policyNumberManager->generateApplicationNumber($policyNumberManager::POLICY_IDENTIFIER);

                if ($insightPolicy->getAppData()->getAgentRateSetID() === null) {
                    $isReferralRequired = true;
                }
                else {
                    $propertyRental = PolicyOptionsManager::getOption(
                        $insightPolicy->getAppData()->getPolicyOptions(),
                        $policyOptionRrp,
                        $insightPolicy->getAppData()->getAmountsCovered()
                    );
                    $isNilExcess = PolicyOptionsManager::isOptionSet(
                        $insightPolicy->getAppData()->getPolicyOptions(),
                        $policyOptionRrpNilExcess,
                        $insightPolicy->getAppData()->getAmountsCovered()
                    );
                    $rateDate = \DateTime::createFromFormat('Y-m-d', $insightPolicy->getAppData()->getEndDate());
                    $rateDate->add(new \DateInterval('P1D'));
                    try {
                        $rateManager = RateDecoratorFactory::getDecorator(
                            'RentRecoveryPlus',
                            $insightPolicy->getAppData()->getAgentRateSetID(),
                            $this->riskArea,
                            $this->iptPercent,
                            $propertyRental,
                            $isNilExcess,
                            $insightPolicy->getRrpData()->getReferenceType(),
                            $insightPolicy->getAppData()->getPolicyLength(),
                            $insightPolicy->getAppData()->isPayMonthly(),
                            $rateDate
                        );

                        $insightPolicy
                            ->setPolicyOptions(
                                $propertyRental,
                                $rateManager->getPremium(),
                                $rateManager->getNilExcessOption()
                            )
                            ->getAppData()
                            ->setPremium($rateManager->getPremium())
                            ->setIpt($rateManager->getIpt())
                            ->setQuote($rateManager->getQuote())
                            ->setRateSetID($rateManager->getRateSetID());

                        $referral->setFromRrpPolicy(
                            $insightPolicy->getRrpData(),
                            $insightPolicy->getAllUnderwritingAnswers(),
                            $now->format('Y-m-d'),
                            $rateManager->getPremium(),
                            $propertyRental,
                            true
                        );
                        $isReferralRequired = $referral->isReferralRequired();
                    }
                    catch (\Exception $ex) {
                        error_log($ex->getMessage());
                        $isReferralRequired = true;
                    }
                }

                $policyNote =
                    "Policy invite sent on renewal for previous Insight policy number {$rrpPolicyNumber}\n" .
                    "Underwriting answers for 'Rent in advance', 'No claims logged' and 'Deposit sum'".
                     " are all assumed for migration from Insight but please check if a claim arises\n";

                try {
                    $this->migratePolicy(
                        $insightPolicy,
                        $rrpPolicyNumber,
                        $newPolicyNumber,
                        $policyNumberManager::WHITE_LABEL_HOMELET,
                        $dateString,
                        $isReferralRequired,
                        $referral->getReferralReason(),
                        $policyNote,
                        $isRenewalInvite
                    );
                }
                catch (\Exception $ex) {
                    error_log($ex->getMessage());
                    $isReferralRequired = true;
                }
                if ($isReferralRequired) {
                    $this->addToReport(self::POLICY_TYPE_REFERRED, $rrpPolicyNumber, $newPolicyNumber);
                }
                else {
                    $this->addToReport(self::POLICY_TYPE_MIGRATED, $rrpPolicyNumber, $newPolicyNumber);
                }
            }
        }

        // Now do exceptions
        $policyList = $searchClient->searchForInsightByEndDate($now, $insightStatusException);
        foreach ($policyList as $rrpPolicyNumber) {

            $insightPolicy->populateByPolicyNumber($rrpPolicyNumber);
            $newPolicyNumber = $policyNumberManager->generateApplicationNumber($policyNumberManager::POLICY_IDENTIFIER);
            $insightPolicy->getAppData()->setPayStatus($this->legacyContainer->get('rrp.legacy.const.pay_status_referred'));
            $policyNote = 'Policy referred as it has changed in Insight since the data was migrated from previous Insight policy number ' . $rrpPolicyNumber;

            try {
                $this->migratePolicy(
                    $insightPolicy,
                    $rrpPolicyNumber,
                    $newPolicyNumber,
                    $policyNumberManager::WHITE_LABEL_HOMELET,
                    $dateString,
                    true,
                    $policyNote,
                    $policyNote
                );
            }
            catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
            $this->addToReport(self::POLICY_TYPE_EXCEPTION, $rrpPolicyNumber, $newPolicyNumber);
        }

        // Now send a report
        $this->sendReport();
    }

    /**
     * Migrates the Insight policy to the live tables
     *
     * @param \RRP\Application\Decorators\RentRecoveryPlusInsight $insightPolicy
     * @param string $rrpPolicyNumber
     * @param string $newPolicyNumber
     * @param string $whiteLabelId
     * @param string $dateString
     * @param bool $isReferralRequired
     * @param string $referralReason
     * @param string $policyNote
     * @param null|bool $isRenewalInvite
     * @return $this
     */
    public function migratePolicy(
        $insightPolicy, $rrpPolicyNumber, $newPolicyNumber, $whiteLabelId, $dateString,
        $isReferralRequired, $referralReason, $policyNote, $isRenewalInvite=null
    ) {
        $agentSchemeNumber = $insightPolicy->getAppData()->getAgentSchemeNumber();

        $customerManager = $this->legacyContainer->get('rrp.legacy.manager.customer');
        $sudoEmailAddress = $customerManager->generateAgentSudoEmailAddress($agentSchemeNumber);
        $customer = $customerManager->getCustomerByEmailAddress($sudoEmailAddress);
        $isNewCustomer = false;
        if ( ! $customer) {
            $customer = $customerManager->createNewCustomer(
                $sudoEmailAddress,
                $this->customerAgent,
                true
            );
            $isNewCustomer = true;

            $agentDatasource = $this->legacyContainer->get('rrp.legacy.datasource.agent');
            /** @var \Model_Core_Agent $agent */
            $agent = $agentDatasource->getAgent($agentSchemeNumber);
            $customer->setLastName($agent->name);
            if ($agent->contact[0]->address->flatNumber) {
                $line1 = $agent->contact[0]->address->flatNumber . ' ';
            }
            else if ($agent->contact[0]->address->houseName) {
                $line1 = $agent->contact[0]->address->houseName . ' ';
            }
            else if ($agent->contact[0]->address->houseNumber) {
                $line1 = $agent->contact[0]->address->houseNumber . ' ';
            }
            else {
                $line1 = '';
            }
            if (
                $agent->contact[0]->address->addressLine1 &&
                $agent->contact[0]->address->addressLine2
            ) {
                $line1 .=
                    $agent->contact[0]->address->addressLine1 . ', ' .
                    $agent->contact[0]->address->addressLine2;
            }
            else if ($agent->contact[0]->address->addressLine1) {
                $line1 .= $agent->contact[0]->address->addressLine1;
            }
            else if ($agent->contact[0]->address->addressLine2) {
                $line1 .= $agent->contact[0]->address->addressLine2;
            }
            $customer->setAddressLine($this->customerAddress1, $line1);
            $customer->setAddressLine(
                $this->customerAddress2,
                $agent->contact[0]->address->town
            );
            $customer->setAddressLine(
                $this->customerAddress3,
                $agent->contact[0]->address->county
            );
            $customer->setPostCode($agent->contact[0]->address->postCode);
            $customer->setCountry($agent->contact[0]->address->country);
            $customerManager->updateCustomer($customer);
        }
        // Now get the reference number from the newly created customer
        $refNo = $customer->getIdentifier($this->customerLegacyIdentifier);

        if ($isNewCustomer) {
            $customerManager->updateCustomerAgentSchemeNumber($agentSchemeNumber, $refNo);
        }

        $policyStartAt = \DateTime::createFromFormat('Y-m-d', $insightPolicy->getAppData()->getStartDate());
        $policyEndAt = \DateTime::createFromFormat('Y-m-d', $insightPolicy->getAppData()->getEndDate());
        try {
            $rateSetId = $this->rates->getRateSetIdForAgent($agentSchemeNumber, $this->riskArea);
        }
        catch (\Exception $ex) {
            $rateSetId = 0;
        }

        // Set the issueDate and timecompleted fields (which oddly record the same value but
        //  in different formats).
        $timeCompleted = time();
        $issueDate = new \DateTime();
        $issueDate->setTimestamp($timeCompleted);

        $insightPolicy
            ->splitName()
            ->setDefaults(
                $rrpPolicyNumber,
                $insightPolicy->getRrpData()->getReferenceType(),
                true,
                $insightPolicy->getRrpData()->getPropertyLetType(),
                $insightPolicy->getRrpData()->getPropertyDeposit(),
                $this->statusPolicy
            )
            ->getAppData()
                ->setRefNo($refNo)
                ->setUnderwritingQuestionSetID($this->underwritingQuestionSetID)
                ->setRiskArea($this->riskArea)
                ->setRateSetID($rateSetId)
                ->setStartDate($policyStartAt)
                ->setEndDate($policyEndAt)
                ->setIssueDate($issueDate)
                ->setTimeCompleted($timeCompleted)
                ->setWhiteLabelID($whiteLabelId)
        ;
        $insightPolicy
            ->getRrpData()
                ->setExistingPolicyRef($rrpPolicyNumber)
                ->setIsContinuationPolicy(true)
        ;

        if ($isReferralRequired) {
            $insightPolicy->getAppData()->setPayStatus($this->payStatusReferred);
        }
        else {
            $insightPolicy->getAppData()->setPayStatus($this->payStatusUpToDate);
        }
        $insightPolicy->save();

        $mailMessage = null;
        // RRPI1000001P for first term, RRPI1000001P-14 for second
        if (strlen($rrpPolicyNumber) == 12) {
            $termNumber = 1;
        }
        else {
            $termNumber = 2;
        }
        if ( ! $insightPolicy->migratePolicy($rrpPolicyNumber, $newPolicyNumber, $termNumber)) {
            $subject = str_replace(
                '{$policyNumber}',
                $rrpPolicyNumber,
                $this->rrpContainer->get('rrp.config.referral.email_subject')
            );
            $mailMessage = <<<EOF
Dear Rent Recovery Plus Insurance Administrator,

An error was encountered when the sending the renewal invite when moving the following policy from Insight

Agent Scheme Number: {$agentSchemeNumber}
Entered On: {$dateString}
RRP Policy Number: {$rrpPolicyNumber}
IAS Policy Number: {$newPolicyNumber}

Thanks
EOF;
        } else if ($isReferralRequired) {
            $subject = str_replace(
                '{$policyNumber}',
                $newPolicyNumber,
                $this->rrpContainer->get('rrp.config.referral.email_subject')
            );
            if (is_array($referralReason)) {
                $reason = implode("\n", $referralReason);
            }
            else {
                $reason = $referralReason;
            }
            $mailMessage = <<<EOF
Policy for as agent {$agentSchemeNumber} has been migrated from Insight.
The following details indicate that the policy needs to be reviewed by Underwriting:

{$reason}

Please contact agent if any further information is required, and refer to Underwriting.
EOF;
        }

        $this->policyNotes->addNote($newPolicyNumber, $policyNote);

        if ($mailMessage) {
            $mailManager = $this->legacyContainer->get('rrp.legacy.mailer');
            $mailManager
                ->setTo(
                    $this->rrpContainer->get('rrp.config.referral.email_to_address'),
                    $this->rrpContainer->get('rrp.config.referral.email_to_name')
                )
                ->setFrom(
                    $this->rrpContainer->get('rrp.config.referral.email_from_address'),
                    $this->rrpContainer->get('rrp.config.referral.email_from_name')
                )
                ->setSubject($subject)
                ->setBodyText($mailMessage)
                ->send();
        }
        else if ($isRenewalInvite) {
            // Send documents
            /** @var \Manager_Insurance_Quote $quoteManager */
            $quoteManager = $this->legacyContainer->get('rrp.legacy.manager.quote');
            $csuId = $this->rrpContainer->get('rrp.config.system_csu_id');
            $quoteManager::sendRenewal($newPolicyNumber, null, null, $csuId);
        }
        return $this;
    }

    /**
     * Initialises the report data
     *
     * @return $this
     */
    private function initialiseReportData()
    {
        $this->reportDescriptions = array(
            self::POLICY_TYPE_MIGRATED =>
                'An invite has been sent for following policies as they are migrating from Insight',
            self::POLICY_TYPE_REFERRED =>
                'An invite has been sent for following policies, but these need to refer',
            self::POLICY_TYPE_EXCEPTION =>
                'The following have been flagged as changed within Insight so require further attention'
        );
        $this->reportPolicies = array();
        for ($type=self::POLICY_TYPE_MIGRATED; $type<=self::POLICY_TYPE_EXCEPTION; $type++) {
            $this->reportPolicies[$type] = array();
        }
        return $this;
    }

    /**
     * Add policy numbers to the report
     *
     * @param int $type one of the POLICY_TYPE_* constants
     * @param string $rrpPolicyNumber
     * @param null|string $newPolicyNumber
     * @return $this
     */
    private function addToReport($type, $rrpPolicyNumber, $newPolicyNumber=null)
    {
        $this->reportPolicies[$type][$rrpPolicyNumber] = $newPolicyNumber;
        return $this;
    }

    /**
     * Send the migration report
     */
    private function sendReport()
    {
        $subject = $this->rrpContainer->get('rrp.config.migration_report.email_subject');
        $message = '';
        $lineBreak = "\r\n";
        for ($type=self::POLICY_TYPE_MIGRATED; $type<=self::POLICY_TYPE_EXCEPTION; $type++) {
            if (count($this->reportPolicies[$type]) > 0) {
                $message .= $this->reportDescriptions[$type] . $lineBreak;
                foreach ($this->reportPolicies[$type] as $rrpPolicyNumber => $newPolicyNumber) {
                    if ($newPolicyNumber) {
                        $message .= sprintf('%s => %s%s', $rrpPolicyNumber, $newPolicyNumber, $lineBreak);
                    }
                    else {
                        $message .= sprintf('%s error in database or rates not set up%s', $rrpPolicyNumber, $lineBreak);

                    }
                }
                $message .= $lineBreak . $lineBreak;
            }
        }
        if (empty($message)) {
            $message = 'No policies were migrated today.';
        }
        $mailManager = $this->legacyContainer->get('rrp.legacy.mailer');
        $mailManager
            ->setTo(
                $this->rrpContainer->get('rrp.config.migration_report.email_to_address'),
                $this->rrpContainer->get('rrp.config.migration_report.email_to_name')
            )
            ->setFrom(
                $this->rrpContainer->get('rrp.config.migration_report.email_from_address'),
                $this->rrpContainer->get('rrp.config.migration_report.email_from_name')
            )
            ->setSubject($subject)
            ->setBodyText($message)
            ->send();

    }
}
