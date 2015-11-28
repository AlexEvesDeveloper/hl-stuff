<?php

namespace RRP\Rate\RateDecorators;

use RRP\Common\ReferenceTypes;
use RRP\Rate\AbstractRateDecorator;
use RRP\Rate\Exception\BandAExceededException;
use RRP\Rate\Exception\BandBExceededException;
use RRP\Rate\Exception\NilExcessNotAllowedException;
use RRP\Rate\Exception\ReferralRequiredException;
use RRP\Rate\Exception\UnhandledPolicyOptionsException;
use RRP\Rate\RateDecoratorInterface;
use RRP\DependencyInjection\LegacyContainer;

use RRP\Model\RentRecoveryPlusRate;

/**
 * Class RentRecoveryPlus
 *
 * @package RRP\Manager\RateDecorators
 * @author April Portus <april.portus@barbon.com>
 */
final class RentRecoveryPlus extends AbstractRateDecorator implements RateDecoratorInterface
{
    /**
     * Band A identifier
     */
    const BAND_A = 'A';

    /**
     * Band A upper limit
     */
    const BAND_A_LIMIT = 2500;

    /**
     * Band B identifier
     */
    const BAND_B = 'B';

    /**
     * Band B upper limit
     */
    const BAND_B_LIMIT = 5000;

    /**
     * @var string - self::BAND_A | self::BAND_B
     */
    protected $band;

    /**
     * @var bool
     */
    protected $isNilExcess;

    /**
     * @var bool
     */
    protected $isFullReference;

    /**
     * @var int
     */
    protected $policyLength;

    /**
     * @var bool
     */
    protected $isPayMonthly;

    /**
     * @var bool
     */
    protected $isRentInAdvance;

    /**
     * @var array
     */
    protected $fullReferenceRates;

    /**
     * @var array
     */
    protected $creditCheckRates;

    /**
     * @param int $agentRateId
     * @param int $riskArea
     * @param float $iptPercent
     * @param float $sumInsured
     * @param bool $isNilExcess
     * @param string $referenceType
     * @param int $policyLength
     * @param bool $isPayMonthly
     * @param \DateTime $policyStartDate
     * @param bool $isRentInAdvance
     */
    public function __construct(
        $agentRateId, $riskArea, $iptPercent, $sumInsured, $isNilExcess, $referenceType, $policyLength, $isPayMonthly,
        \DateTime $policyStartDate, $isRentInAdvance=false
    )
    {
        $this->agentRate = $agentRateId;
        $this->riskArea = $riskArea;
        $this->iptPercent = $iptPercent;

        $container = new LegacyContainer();
        /** @var \Datasource_Insurance_RentRecoveryPlus_Rates $ratesDataSource */
        $ratesDataSource = $container->get('rrp.legacy.datasource.rrp_rates');
        $rateSet = $ratesDataSource->getRateSet($agentRateId, $riskArea, $policyStartDate);
        /** @var RentRecoveryPlusRate $rates */
        $rates = RentRecoveryPlusRate::hydrate($rateSet);
        $this->rateSetId = $rates->getRateSetId();

        $this->fullReferenceRates = array();
        // For full references   policy length
        //                        |  is Nil Excess
        //                        |  |  Band A/B
        $this->fullReferenceRates[ 6][0][self::BAND_A] = $rates->getRentGuaranteeRrpFullRef6mBandA();
        $this->fullReferenceRates[ 6][0][self::BAND_B] = $rates->getRentGuaranteeRrpFullRef6mBandB();
        $this->fullReferenceRates[12][0][self::BAND_A] = $rates->getRentGuaranteeRrpFullRef12mBandA();
        $this->fullReferenceRates[12][0][self::BAND_B] = $rates->getRentGuaranteeRrpFullRef12mBandB();
        $this->fullReferenceRates[ 6][1][self::BAND_A] =
            $rates->getRentGuaranteeRrpFullRef6mBandA() + $rates->getRentGuaranteeRrpFullRef6mNilExcessBandA();
        $this->fullReferenceRates[ 6][1][self::BAND_B] =
            $rates->getRentGuaranteeRrpFullRef6mBandB() + $rates->getRentGuaranteeRrpFullRef6mNilExcessBandB();
        $this->fullReferenceRates[12][1][self::BAND_A] =
            $rates->getRentGuaranteeRrpFullRef12mBandA() + $rates->getRentGuaranteeRrpFullRef12mNilExcessBandA();
        $this->fullReferenceRates[12][1][self::BAND_B] =
            $rates->getRentGuaranteeRrpFullRef12mBandB() + $rates->getRentGuaranteeRrpFullRef12mNilExcessBandB();

        $this->creditCheckRates = array();
        // For credit checks   policy length
        //                      |  Band A/B
        $this->creditCheckRates[12] = $rates->getRentGuaranteeRrpCreditCheck12m();
        $this->creditCheckRates[ 6] = $rates->getRentGuaranteeRrpCreditCheck6m();

        $this
            ->setCoverAmounts($sumInsured, $isNilExcess, $referenceType, $policyLength, $isPayMonthly, $isRentInAdvance)
            ->calculatePremium();
    }

    /**
     * Sets the cover levels
     *
     * @param float $sumInsured
     * @param bool $isNilExcess
     * @param string $referenceType
     * @param int $policyLength
     * @param bool $isPayMonthly
     * @param bool $isRentInAdvance
     * @return $this
     * @throws \RRP\Rate\Exception\BandAExceededException
     * @throws \RRP\Rate\Exception\BandBExceededException
     * @throws \RRP\Rate\Exception\UnhandledPolicyOptionsException
     * @throws \RRP\Rate\Exception\NilExcessNotAllowedException
     */
    public function setCoverAmounts(
        $sumInsured, $isNilExcess, $referenceType, $policyLength, $isPayMonthly, $isRentInAdvance=false
    ) {
        $this->isFullReference = ReferenceTypes::isFullReference($referenceType);

        if ($sumInsured > self::BAND_B_LIMIT) {
            throw new BandBExceededException();
        }
        else if ($sumInsured > self::BAND_A_LIMIT) {
            if ($this->isFullReference) {
                $this->band = self::BAND_B;
            }
            else {
                throw new BandAExceededException();
            }
        }
        else {
            $this->band = self::BAND_A;
        }

        if (
            ! $this->isFullReference &&
            $this->band != self::BAND_A
        ) {
            throw new NilExcessNotAllowedException();
        }
        else {
            $this->isNilExcess = $isNilExcess;
        }
        if ($this->isFullReference) {
            if ( ! isset($this->fullReferenceRates[$policyLength][$isNilExcess][$this->band])) {
                $error = sprintf('PolicyLength:%d isNilExcess:%d Band:%d', $policyLength, $isNilExcess, $this->band);
                throw new UnhandledPolicyOptionsException($error);
            }
        }
        else if ( ! isset($this->creditCheckRates[$policyLength])) {
            $error = sprintf('PolicyLength:%d', $policyLength);
            throw new UnhandledPolicyOptionsException($error);
        }
        $this->policyLength = $policyLength;
        $this->isPayMonthly = $isPayMonthly;
        $this->isRentInAdvance  = $isRentInAdvance;
        $this->calculatePremium();
        return $this;
    }

    /**
     * Calculates the premium depending on using the rate model
     *
     * @return $this
     * @throws \RRP\Rate\Exception\ReferralRequiredException
     */
    private function calculatePremium()
    {
        $this->nilExcessOption = 0.0;
        if ($this->isFullReference) {
            $this->premium = $this->fullReferenceRates[$this->policyLength][$this->isNilExcess][$this->band];
            if ($this->isNilExcess) {
                $this->nilExcessOption =
                    $this->fullReferenceRates[$this->policyLength][1][$this->band] -
                    $this->fullReferenceRates[$this->policyLength][0][$this->band];
            }
        }
        else {
            $this->premium = $this->creditCheckRates[$this->policyLength];
        }
        if ($this->isPayMonthly) {
            $this->premium = round($this->premium / (float)$this->policyLength, 2);
            $this->nilExcessOption = round($this->nilExcessOption / (float)$this->policyLength, 2);
        }
        if ($this->premium <= 0.0) {
            throw new ReferralRequiredException();
        }
        return $this;
    }
}