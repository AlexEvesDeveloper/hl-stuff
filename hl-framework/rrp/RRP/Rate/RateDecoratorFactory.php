<?php

namespace RRP\Rate;

use RRP\Rate\Exception\UnknownCoverNameException;

/**
 * Class RateDecoratorFactory
 *
 * @package RRP\Rate
 * @author April Portus <april.portus@barbon.com>
 */
class RateDecoratorFactory
{
    /**
     * RentRecoveryPlus decorator type
     */
    const RENT_RECOVERY_PLUS_RATE = 'RentRecoveryPlus';

    /**
     * Gets the rate decorator
     *
     * @param string $coverName
     * @param int $agentRateId
     * @param int $riskArea
     * @param float $iptPercent
     * @param float $sumInsured
     * @param bool $isNilExcess
     * @param string $referenceType
     * @param int $policyLength
     * @param bool $isPayMonthly
     * @param \DateTime $policyStartDate
     * @return mixed
     * @throws Exception\UnknownCoverNameException
     */
    public static function getDecorator(
        $coverName, $agentRateId, $riskArea, $iptPercent, $sumInsured, $isNilExcess, $referenceType, $policyLength,
        $isPayMonthly, \DateTime $policyStartDate
    ) {
        $className = 'RRP\Rate\RateDecorators\\' . $coverName;

        switch ($coverName) {
            case self::RENT_RECOVERY_PLUS_RATE:
                return new $className(
                    $agentRateId,
                    $riskArea,
                    $iptPercent,
                    $sumInsured,
                    $isNilExcess,
                    $referenceType,
                    $policyLength,
                    $isPayMonthly,
                    $policyStartDate
                );

            default:
                throw new UnknownCoverNameException();
        }
    }
}