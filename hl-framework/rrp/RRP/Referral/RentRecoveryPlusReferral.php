<?php

namespace RRP\Referral;

use RRP\Application\Decorators\RentRecoveryPlusQuote;
use RRP\Common\ReferenceTypes;
use RRP\Common\TenancyAgreementTypes;
use RRP\Model\RentRecoveryPlusApplication;
use RRP\Model\RentRecoveryPlusMta;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Referral\Exception\ReferralSourceNotSetException;
use RRP\Underwriting\Decorators\RentRecoveryPlusAnswers;

/**
 * Class RentRecoveryPlusReferral
 *
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusReferral
{
    /**
     * Used to identify that the data has no source yet
     */
    const SOURCE_NOT_SET = 0;

    /**
     * Used to identify that the source of the data is from an application
     */
    const SOURCE_APPLICATION = 1;

    /**
     * Used to identify that the source of the data is from rent recovery plus
     */
    const SOURCE_RENT_RECOVERY_PLUS = 2;

    /**
     * Used to identify that the source of the data is from an mid-term adjustment
     */
    const SOURCE_MID_TERM = 3;

    /**
     * @var int one of the SOURCE_* constants
     */
    private $source;

    /**
     * @var bool
     */
    private $hasPossibleClaimCircumstances;

    /**
     * @var string
     */
    private $claimInfo;

    /**
     * @var bool
     */
    private $hasPermittedOccupiersOnly;

    /**
     * @var bool
     */
    private $hasTenancyDisputes;

    /**
     * @var float
     */
    private $propertyRental;

    /**
     * @var float
     */
    private $propertyDeposit;

    /**
     * @var bool
     */
    private $hasPaidRentInAdvance;

    /**
     * @var bool
     */
    private $hasPriorClaims;

    /**
     * @var bool
     */
    private $isDepositSufficient;

    /**
     * @var bool
     */
    private $isAssuredShortholdTenancy;

    /**
     * @var string
     */
    private $referenceType;

    /**
     * @var string
     */
    private $otherProvider;

    /**
     * @var bool
     */
    private $isContinuationOfExistingPolicy;

    /**
     * @var string
     */
    private $existingPolicyRef;

    /**
     * @var float
     */
    private $premium;

    /**
     * @var \DateTime
     */
    private $policyStartAt;

    /**
     * @var bool
     */
    private $hasProviderBeenChecked;

    /**
     * @var array
     */
    private $referenceBasedReferralReasons;

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var Model_Core_Agent
     */
    private $agent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->source = self::SOURCE_NOT_SET;
        $this->referenceBasedReferralReasons = array();
    }

    /**
     * Sets the data using the application as the source
     *
     * @param RentRecoveryPlusApplication $application
     * @param float $premium
     */
    public function setFromApplication(RentRecoveryPlusApplication $application, $premium)
    {
        $this->hasPossibleClaimCircumstances = $application->getHasPossibleClaimCircumstances();
        $this->hasPermittedOccupiersOnly = $application->getHasPermittedOccupiersOnly();
        $this->hasTenancyDisputes = $application->getHasTenancyDisputes();
        $this->hasPaidRentInAdvance = $application->getHasPaidRentInAdvance();
        $this->hasPriorClaims = $application->getHasPriorClaims();
        $this->isDepositSufficient = $application->getIsDepositSufficient();
        $this->propertyRental = $application->getPropertyRental();
        $this->propertyDeposit = $application->getPropertyDeposit();
        $this->isAssuredShortholdTenancy =
            TenancyAgreementTypes::isAssuredShortholdTenancy($application->getTenancyAgreementType());
        $this->claimInfo = $application->getClaimInfo();
        $this->otherProvider = $application->getOtherProvider();
        $this->referenceType = $application->getReferenceType();
        $this->isContinuationOfExistingPolicy = $application->getIsContinuationOfExistingPolicy();
        $this->policyStartAt = $application->getPolicyStartAt();
        $this->premium = $premium;
        $this->hasProviderBeenChecked = false;
        $this->source = self::SOURCE_APPLICATION;
    }

    /**
     * Sets the data using the application as the source
     *
     * @param RentRecoveryPlusQuote $quote
     * @param float $propertyRental
     * @param null|bool $hasProviderBeenChecked
     */
    public function setFromRrpPolicy(RentRecoveryPlusQuote $quote, $propertyRental, $hasProviderBeenChecked = null)
    {
        $rrpPolicy = $quote->getRrpData();
        $underwritingAnswers = $quote->getAllUnderwritingAnswers();
        $application = $quote->getAppData();


        $this->hasPossibleClaimCircumstances =
            $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CLAIM_CIRCUMSTANCES];
        $this->hasPermittedOccupiersOnly =
            $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PERMITTED_OCCUPIERS];
        $this->hasTenancyDisputes = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_DISPUTES];
        $this->hasPaidRentInAdvance = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_RENT_IN_ADVANCE];
        $this->hasPriorClaims = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PRIOR_CLAIMS];
        $this->isDepositSufficient = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_DEPOSIT_SUFFICIENT];
        $this->propertyRental = $propertyRental;
        $this->propertyDeposit = $rrpPolicy->getPropertyDeposit();
        $this->isAssuredShortholdTenancy = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_AST];
        $this->claimInfo = $rrpPolicy->getClaimInfo();
        $this->otherProvider = $rrpPolicy->getOtherProvider();
        $this->referenceType = $rrpPolicy->getReferenceType();
        $this->isContinuationOfExistingPolicy = $underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CONTINUATION];
        $this->policyStartAt = \DateTime::createFromFormat('Y-m-d', $application->getStartDate());
        $this->premium = $application->getPremium();
        $this->hasProviderBeenChecked = (null === $hasProviderBeenChecked ? false : $hasProviderBeenChecked);
        $this->source = self::SOURCE_RENT_RECOVERY_PLUS;
    }

    /**
     * Sets the data using the mid-term adjustment as the source
     *
     * @param RentRecoveryPlusMta $mta
     * @param float $premium
     */
    public function setFromMta(RentRecoveryPlusMta $mta, $premium)
    {
        $this->hasPossibleClaimCircumstances = $mta->getHasPossibleClaimCircumstances();
        $this->hasPermittedOccupiersOnly = $mta->getHasPermittedOccupiersOnly();
        $this->hasTenancyDisputes = $mta->getHasTenancyDisputes();
        $this->hasPriorClaims = $mta->getHasPriorClaims();
        $this->isDepositSufficient = $mta->getIsDepositSufficient();
        $this->propertyRental = $mta->getPropertyRental();
        $this->isAssuredShortholdTenancy =
            TenancyAgreementTypes::isAssuredShortholdTenancy($mta->getTenancyAgreementType());
        $this->claimInfo = $mta->getClaimInfo();
        $this->premium = $premium;
        $this->hasProviderBeenChecked = true;
        $this->source = self::SOURCE_MID_TERM;
    }

    /**
     * Determines whether a referral is required
     *
     * @return bool
     * @throws ReferralSourceNotSetException
     */
    public function isReferralRequired()
    {
        switch ($this->source) {
            case self::SOURCE_APPLICATION:
            case self::SOURCE_RENT_RECOVERY_PLUS:
                if (
                    $this->propertyDeposit <= 0.0 ||
                    ! $this->hasPaidRentInAdvance ||
                    ( ! $this->hasProviderBeenChecked && ReferenceTypes::isProviderRequired($this->referenceType)) ||
                    $this->isContinuationOfExistingPolicy ||
                    $this->premium <= 0.0
                ) {
                    return true;
                }
                $now = new \DateTime();
                $now->setTime(0, 0, 0);
                if ($this->policyStartAt < $now) {
                    return true;
                }
            // continue through for the MTA specific checks as these are valid for an application as well
            case self::SOURCE_MID_TERM:
                // All is good with the world
                if (
                    $this->hasPossibleClaimCircumstances ||
                    ! $this->hasPermittedOccupiersOnly ||
                    $this->hasTenancyDisputes ||
                    $this->hasPriorClaims ||
                    ! $this->isDepositSufficient ||
                    $this->propertyRental > RentRecoveryPlus::BAND_B_LIMIT ||
                    ! $this->isAssuredShortholdTenancy
                ) {
                    return true;
                }
                break;
            default:
                throw new ReferralSourceNotSetException();
        }
        return false;
    }

    /**
     * Gets an array of referral reasons
     *
     * @return array
     */
    public function getReferralReason()
    {
        $reasons = array();
        if ($this->hasPossibleClaimCircumstances) {
            $reasons[] = 'There are circumstances that may give rise to a claim as follows:';
            $reasons[] = '  ' . $this->claimInfo;
        }
        if ( ! $this->hasPermittedOccupiersOnly) {
            $reasons[] = 'The property has does not have only permitted occupiers.';
        }
        if ($this->hasTenancyDisputes) {
            $reasons[] = 'There are tenancy disputes or late payment of rent issues.';
        }
        if ($this->hasPriorClaims) {
            $reasons[] = 'There have been claims logged during the existing tenancy.';
        }
        if ( ! $this->isDepositSufficient) {
            $reasons[] = 'A deposit with a sum equivalent to (or greater than) 1 months rent was not taken prior to the commencement of the tenancy.';
        }
        if ($this->propertyRental > RentRecoveryPlus::BAND_B_LIMIT) {
            $reasons .=
                'There monthly rental exceeds the upper limit of £' .
                RentRecoveryPlus::BAND_B_LIMIT;
        }
        if (
            $this->propertyDeposit <= 0.0 &&
            (self::SOURCE_APPLICATION == $this->source ||
                self::SOURCE_RENT_RECOVERY_PLUS == $this->source)
        ) {
            $reasons[] = 'No deposit has been paid.';
        }
        if (
            ! $this->hasPaidRentInAdvance &&
            (self::SOURCE_APPLICATION == $this->source ||
                self::SOURCE_RENT_RECOVERY_PLUS == $this->source)) {
            $reasons[] = 'No rent has been paid in advance.';
        }
        if ( ! $this->isAssuredShortholdTenancy) {
            $reasons[] = 'There is not a Assured Shorthold Tenancy agreement.';
        }
        if (
            ! $this->hasProviderBeenChecked &&
            ReferenceTypes::isProviderRequired($this->referenceType) &&
            (self::SOURCE_APPLICATION == $this->source ||
                self::SOURCE_RENT_RECOVERY_PLUS == $this->source)
        ) {
            if ($this->otherProvider == '') {
                $reasons[] = 'The reference was provided by another provider';
            }
            else {
                $reasons[] = 'The reference was provided by ' . $this->otherProvider;
            }
        }
        if (
            $this->isContinuationOfExistingPolicy &&
            (self::SOURCE_APPLICATION == $this->source ||
                self::SOURCE_RENT_RECOVERY_PLUS == $this->source)
        ) {
            $reasons[] .=
                'This is the continuation of existing policy ' .$this->existingPolicyRef .
                '. As such the no claims period is waived so we need to consider if it is acceptable' .
                ' and to ensure the relevant endorsement is applied if it is agreed.';
        }
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        if ($this->policyStartAt < $now) {
            $reasons[] .= 'The policy start date has already passed.';
        }
        if (count($reasons) == 0) {
            $reasons[] = 'The premium calculation failed';
        }
        return $reasons;
    }

    /**
     * Set $referenceBasedReferralReasons.
     *
     * @param array $reasons
     * @return $this
     */
    public function setReferenceBasedReferralReasons(array $reasons)
    {
        $this->referenceBasedReferralReasons = $reasons;
        return $this;
    }

    /**
     * Get $referenceBasedReferralReasons.
     *
     * @return array
     */
    public function getReferenceBasedReferralReasons()
    {
        return $this->referenceBasedReferralReasons;
    }

    /**
     * A wrapper for $this->getReferralReason
     *
     * This makes the classes API cleaner by giving it a name consistent with $this->getReferenceBasedReferralReasons.
     *
     * @return array
     */
    public function getApplicationBasedReferralReasons()
    {
        $reasons = $this->isReferralRequired() ? $this->getReferralReason() : array();
        return $reasons;
    }

    /**
     * Aggregates all referral reasons into one array.
     *
     * @return array
     */
    public function getAllReferralReasons()
    {
        return array_merge($this->getReferenceBasedReferralReasons(), $this->getApplicationBasedReferralReasons());
    }

    /**
     * Get $policyNumber.
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Set $policyNumber.
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Get $agent.
     *
     * @return Model_Core_Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set $agent.
     *
     * @param $agent
     * @return $this
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }
}