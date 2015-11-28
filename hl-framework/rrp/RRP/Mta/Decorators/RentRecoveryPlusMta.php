<?php

namespace RRP\Mta\Decorators;

use RRP\Application\ApplicationDecoratorFactory;
use RRP\Common\ReferenceTypes;
use RRP\Common\TenancyAgreementTypes;
use RRP\Mta\AbstractMtaDecorator;
use RRP\Mta\MtaDecoratorInterface;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Referral\RentRecoveryPlusReferral;
use RRP\Transaction\TransactionDecoratorFactory;
use RRP\Underwriting\UnderwritingDecoratorFactory;
use RRP\Underwriting\Decorators\RentRecoveryPlusAnswers;
use RRP\Utility\PolicyOptionsManager;
use RRP\Utility\ProRataCalculations;

/**
 * Class RentRecoveryPlusMta
 *
 * @package RRP\Mta\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusMta extends AbstractMtaDecorator implements MtaDecoratorInterface
{
    /**
     * @var \Datasource_Insurance_Policy_Term
     */
    public $policyTerm;

    /**
     * @var \Datasource_Insurance_PolicyNotes
     */
    public $policyNotes;

    /**
     * @var \Datasource_Insurance_Policy_Cover
     */
    private $policyCover;

    /**
     * @var \Datasource_Insurance_Policy_Options
     */
    private $policyOptions;

    /**
     * @var \Datasource_Insurance_Disbursement
     */
    private $disbursement;

    /**
     * @var \Datasource_Core_Transaction
     */
    private $transaction;

    /**
     * @var \Datasource_Core_TransactionSupport
     */
    private $transactionSupport;

    /**
     * @var string parameterised constant
     */
    private $mtaStatusLive;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('rrp.legacy.datasource.mta');
        $this->policyTerm = $this->container->get('rrp.legacy.datasource.policy_term');
        $this->policyNotes = $this->container->get('rrp.legacy.datasource.policy_notes');
        $this->policyCover = $this->container->get('rrp.legacy.datasource.policy_cover');
        $this->policyOptions = $this->container->get('rrp.legacy.datasource.policy_options');
        $this->disbursement = $this->container->get('rrp.legacy.datasource.disbursement');
        $this->transaction = $this->container->get('rrp.legacy.datasource.transaction');
        $this->transactionSupport = $this->container->get('rrp.legacy.datasource.transaction_support');
        $this->mtaStatusLive = $this->container->get('rrp.legacy.const.mta_status_live');
    }

    /**
     * @inheritdoc
     */
    public function accept($policyNumber, $mtaId)
    {
        /** @var \RRP\Application\Decorators\RentRecoveryPlusPolicy $policy */
        $policy = ApplicationDecoratorFactory::getDecorator('RentRecoveryPlusPolicy');
        $policy->populateByPolicyNumber($policyNumber);

        $this->legacyMtaData = $this->legacyMta->getByMtaID($mtaId);

        $this->rrpMtaData = $this->rrpMta->getRentRecoveryPlusMta($mtaId);

        $proRataPremium = $policy->getAppData()->getPremium() + $this->legacyMtaData->getPremium();
        $proRataQuote = $policy->getAppData()->getQuote() + $this->legacyMtaData->getQuote();
        $proRataIpt = $policy->getAppData()->getIpt() + $this->legacyMtaData->getIpt();
        $propertyRental = PolicyOptionsManager::getOption(
            $this->legacyMtaData->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
            $this->legacyMtaData->getAmountscovered()
        );
        $isNilExcess = PolicyOptionsManager::isOptionSet(
            $this->legacyMtaData->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
            $this->legacyMtaData->getAmountscovered()
        );
        $policyPremium = PolicyOptionsManager::getOption(
            $policy->getAppData()->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
            $policy->getAppData()->getOptionPremiums()
        );
        $mtaPremium = PolicyOptionsManager::getOption(
            $this->legacyMtaData->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
            $this->legacyMtaData->getOptionPremiums()
        );
        $optionPremiums = PolicyOptionsManager::addPolicyOption($policyPremium+$mtaPremium);
        if ($isNilExcess) {
            $policyNilExcessOption = PolicyOptionsManager::getOption(
                $policy->getAppData()->getPolicyOptions(),
                $this->getContainer()->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $policy->getAppData()->getOptionPremiums()
            );
            $mtaNilExcessOption = PolicyOptionsManager::getOption(
                $this->legacyMtaData->getPolicyOptions(),
                $this->getContainer()->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $this->legacyMtaData->getOptionPremiums()
            );
            $optionPremiums = PolicyOptionsManager::addPolicyOption(
                $policyNilExcessOption + $mtaNilExcessOption,
                $optionPremiums
            );
        }
        $policy->getAppData()
            ->setPolicyOptions($this->legacyMtaData->getPolicyOptions())
            ->setAmountsCovered($this->legacyMtaData->getAmountscovered())
            ->setOptionPremiums($optionPremiums)
            ->setPremium($proRataPremium)
            ->setQuote($proRataQuote)
            ->setIpt($proRataIpt);
        $policy->getRrpData()
            ->setClaimInfo($this->rrpMtaData->getClaimInfo());
        $disbData = $this->disbursement->getDisbursement(
            $policy->getAppData()->getWhiteLabelID(),
            $policy->getAppData()->getRateSetID()
        );
        if ( ! $disbData) {
            $message = sprintf(
                'Disbursement data not found (WhiteLabelId = %s, RateSetID = %d)',
                $policy->getAppData()->getWhiteLabelID(),
                $policy->getAppData()->getRateSetID()
            );
            throw new \RuntimeException($message);
        }
        $getter = sprintf(
            'getRrpi%s%dm',
            (ReferenceTypes::isFullReference($policy->getRrpData()->getReferenceType()) ? 'FullRef' : 'CreditCheck'),
            $policy->getAppData()->getPolicyLength()
        );
        if ($isNilExcess) {
            $getter .= '0xs';
        }
        if ( ! method_exists($disbData, $getter)) {
            $message = sprintf('Unknown disbursement type (%s)', $getter);
            throw new \RuntimeException($message);
        }
        $disbursement = $disbData->{$getter}();
        if (empty($disbursement)) {
            $message = sprintf(
                'Disbursement data not set (WhiteLabelId = %s, RateSetID = %d, %s)',
                $policy->getAppData()->getWhiteLabelID(),
                $policy->getAppData()->getRateSetID(),
                $getter
            );
            throw new \RuntimeException($message);
        }
        $termData = $this->policyTerm->getPolicyTerm(
            $policy->getAppData()->getPolicyNumber(),
            $policy->getAppData()->getStartDate()
        );
        if ( ! $termData->id) {
            throw new \RuntimeException('Invalid Term Id');
        }

        if ($propertyRental < RentRecoveryPlus::BAND_A_LIMIT) {
            $band = $disbData::BAND_A;
            $transBand = RentRecoveryPlus::BAND_A;
        }
        else {
            $band = $disbData::BAND_B;
            $transBand = RentRecoveryPlus::BAND_B;
        }
        $insuranceNetAmount = $disbData->extractBandDisbursement($disbursement, $band);

        $proRataCalcs = new ProRataCalculations(
            $policy->getAppData()->getStartDate(),
            $this->legacyMtaData->getDateOnRisk()
        );

        $netPremium = $proRataCalcs->getProRata(
            $policy->getAppData()->getPolicyLength(),
            $policy->getAppData()->getPolicyLength(),
            $insuranceNetAmount,
            $termData->netPremium
        );
        $this->policyTerm->updatePolicyTerm($policy->getAppData(), $termData->term, array('netPremium' => $netPremium));

        $this->policyNotes->addNote($policyNumber, $this->legacyMtaData->getDisplayNotes());

        if ($policy->save()) {
            if ($this->legacyMta->updateStatus($policyNumber, $mtaId, $this->mtaStatusLive)) {
                $coverOptions = array();
                foreach ($policy->getAppData()->getValidPolicyOptionNames() as $optionName) {
                    if (PolicyOptionsManager::isOptionSet(
                        $policy->getAppData()->getPolicyOptions(),
                        $optionName,
                        $policy->getAppData()->getAmountsCovered()
                    )) {
                        $optionId = $this->policyOptions->fetchOptionsByName($optionName);

                        $sumInsured = PolicyOptionsManager::getOption(
                            $policy->getAppData()->getPolicyOptions(),
                            $optionName,
                            $policy->getAppData()->getAmountsCovered()
                        );

                        $premium = PolicyOptionsManager::getOption(
                            $policy->getAppData()->getPolicyOptions(),
                            $optionName,
                            $policy->getAppData()->getOptionPremiums()
                        );

                        $coverOptions[] = array(
                            'policyNumber' => $policyNumber,
                            'policyOptionID' => $optionId,
                            'sumInsured' => $sumInsured,
                            'premium' => $premium
                        );
                    }
                }
                $this->policyCover->setCover($policyNumber, $coverOptions);

                if ($this->legacyMtaData->getPremium() == 0) {
                    //If no premium change there's no need to store anything in the transaction
                    return true;
                }

                $transaction = TransactionDecoratorFactory::getDecorator(
                    TransactionDecoratorFactory::TRANSACTION_UPDATE,
                    $policy->getAppData()->getPremium(),
                    $insuranceNetAmount,
                    $termData->id,
                    $mtaId,
                    $transBand,
                    $this->legacyMtaData->getDateOnRisk(),
                    $policy->getAppData()
                );
                $transaction->processAllTransactions();

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function create($policy, $mta, $premium, $quote, $ipt, $nilExcessOption)
    {
        $this->legacyMtaData->setPolicyNumber($policy->getAppData()->getPolicyNumber());
        $now = new \DateTime();
        $isNilExcess = PolicyOptionsManager::isOptionSet(
            $policy->getAppData()->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
            $policy->getAppData()->getAmountsCovered()
        );
        $proRataCalcs = new ProRataCalculations(
            $mta->getPolicyStartedAt(),
            $mta->getMtaEffectiveAt()
        );
        $premiumAdjustment = $proRataCalcs->getAdjustment(
            $policy->getAppData()->getPolicyLength(),
            $policy->getAppData()->getPolicyLength(),
            $premium,
            $policy->getAppData()->getPremium()
        );
        $quoteAdjustment = $proRataCalcs->getAdjustment(
            $policy->getAppData()->getPolicyLength(),
            $policy->getAppData()->getPolicyLength(),
            $quote,
            $policy->getAppData()->getQuote()
        );
        $iptAdjustment = $proRataCalcs->getAdjustment(
            $policy->getAppData()->getPolicyLength(),
            $policy->getAppData()->getPolicyLength(),
            $ipt,
            $policy->getAppData()->getIpt()
        );
        $amountToPay = $quoteAdjustment;
        $monthsRemaining = $proRataCalcs->getMonthsRemaining(
            $policy->getAppData()->getPolicyLength()
        );

        $policyOptions = PolicyOptionsManager::addPolicyOption(
            $this->container->get('rrp.legacy.const.policy_option_rrp')
        );
        $amountsCovered = PolicyOptionsManager::addPolicyOption($mta->getPropertyRental());
        $policyOptionPremium = PolicyOptionsManager::getOption(
            $policy->getAppData()->getPolicyOptions(),
            $this->container->get('rrp.legacy.const.policy_option_rrp'),
            $policy->getAppData()->getOptionPremiums()
        );
        $policyOptionNilExcess = 0.0;
        if ($isNilExcess) {
            $policyOptionNilExcess = PolicyOptionsManager::getOption(
                $policy->getAppData()->getPolicyOptions(),
                $this->container->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $policy->getAppData()->getOptionPremiums()
            );
        }
        $policyOptionAdjustment = $proRataCalcs->getAdjustment(
            $policy->getAppData()->getPolicyLength(),
            $policy->getAppData()->getPolicyLength(),
            $premium - $nilExcessOption,
            $policyOptionPremium
        );
        $optionPremiums = PolicyOptionsManager::addPolicyOption($policyOptionAdjustment);
        if ($isNilExcess) {
            $nilExcessAdjustment = $proRataCalcs->getAdjustment(
                $policy->getAppData()->getPolicyLength(),
                $policy->getAppData()->getPolicyLength(),
                $nilExcessOption,
                $policyOptionNilExcess
            );
            $policyOptions = PolicyOptionsManager::addPolicyOption(
                $this->container->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $policyOptions);
            $amountsCovered = PolicyOptionsManager::addPolicyOption($mta->getPropertyRental(), $amountsCovered);
            $optionPremiums = PolicyOptionsManager::addPolicyOption($nilExcessAdjustment, $optionPremiums);
        }
        $this->legacyMtaData
            ->setPolicyOptions($policyOptions)
            ->setAmountsCovered($amountsCovered)
            ->setOptionPremiums($optionPremiums)
            ->setDateAdded($now->format('Y-m-d'))
            ->setStatus('pending')
            ->setPremium($premiumAdjustment)
            ->setQuote($quoteAdjustment)
            ->setIpt($iptAdjustment)
            ->setAmountToPay($amountToPay)
            ->setMonthsRemaining($monthsRemaining)
            ->setAdminCharge(0.0)
            ->setPropAddress1('')
            ->setPropAddress3('')
            ->setPropAddress5('')
            ->setPropPostcode('')
            ->setRiskArea('')
            ->setRiskAreaB('')
            ->setChangeCorrespondenceAndPersonal('')
            ->setParagonMortgageNumber('');

        $propertyRental = PolicyOptionsManager::getOption(
            $policy->getAppData()->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
            $policy->getAppData()->getAmountsCovered()
        );
        $notes =
            $now->format('d/m/Y') . "\n" .
            "MTA by Agent via Connect\n";
        if ($mta->getPropertyRental() != $propertyRental) {
            $notes .=
                "Property rental changed from £" . round($propertyRental, 2) .
                " to £" . round($mta->getPropertyRental(), 2) . "\n";
        }

        $referral = new RentRecoveryPlusReferral();
        $referral->setFromMta($mta, $premium);
        if ($referral->isReferralRequired()) {
            $notes .=
                "\nThis policy has been referred for the following reason(s)\n\n" .
                implode("\n", $referral->getReferralReason());
        }
        $this->legacyMtaData->SetDisplayNotes($notes);
        $endTime = new \DateTime($policy->getAppData()->getEndDate());
        $monthsRemaining = (int)$endTime->format('m') - (int)$now->format('m');
        if ($monthsRemaining < 0) {
            $monthsRemaining += 13;
        }
        else {
            $monthsRemaining += 1;
        }
        $this->legacyMtaData
            ->setMonthsRemaining($monthsRemaining)
            ->setPaidNet('no')
            ->setDateAdded($now->format('Y-m-d'))
            ->setDateOnRisk($mta->getMtaEffectiveAt())
            ->setDateOffRisk(null)
        ;

        /** @var RentRecoveryPlusAnswers $underwriting */
        $underwriting = UnderwritingDecoratorFactory::getDecorator(
            UnderwritingDecoratorFactory::UNDERWRITING_RENT_RECOVERY_PLUS,
            $this->container->get('rrp.config.underwriting_question_set_id'),
            $now->format('Y-m-d'),
            $policy->getAppData()->getPolicyNumber()
        );
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CONTINUATION] = null;
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_RENT_IN_ADVANCE] = null;
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CLAIM_CIRCUMSTANCES] =
            $mta->getHasPossibleClaimCircumstances();
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PERMITTED_OCCUPIERS] =
            $mta->getHasPermittedOccupiersOnly();
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_DISPUTES] =
            $mta->getHasTenancyDisputes();
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_AST] =
            TenancyAgreementTypes::isAssuredShortholdTenancy($mta->getTenancyAgreementType());
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_DEPOSIT_SUFFICIENT] =
            $mta->getIsDepositSufficient();
        $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PRIOR_CLAIMS] =
            $mta->getHasPriorClaims();
        $underwriting
            ->setAnswers($underwritingAnswers)
            ->saveAnswers();

        $mtaID = $this->legacyMta->create($this->legacyMtaData);
        if ($mtaID) {
            if ($this->rrpMta->create($mta, $mtaID)) {
                return $mtaID;
            }
        }
        return false;
    }
}