<?php

namespace RRP\Utility;

/**
 * Class ProRataCalculations
 *
 * @package RRP\Utility
 * @author April Portus <april.portus@barbon.com>
 */
class ProRataCalculations
{
    /**
     * @var \DateTime
     */
    private $startAt;

    /**
     * @var \DateTime
     */
    private $effectiveAt;

    /**
     * Constructor
     *
     * @param string $dateStarted
     * @param string $dateEffective
     */
    public function __construct($dateStarted, $dateEffective)
    {
        $this->startAt = new \DateTime($dateStarted);
        $this->effectiveAt = new \DateTime($dateEffective);
    }

    /**
     * Gets the pro-rata amount
     *
     * @param int $newPolicyLength
     * @param int $previousPolicyLength
     * @param float $newAmount
     * @param float $previousAmount
     * @return float
     */
    public function getProRata($newPolicyLength, $previousPolicyLength, $newAmount, $previousAmount)
    {
        $paidSoFar = (float) $this->getPaidSoFar($previousPolicyLength, $previousAmount);
        $remainder = (float) $this->getRemainderToPay($newPolicyLength, $newAmount);
        return round($paidSoFar + $remainder, 2);
    }

    /**
     * Gets the adjustment amount
     *
     * @param int $policyLength
     * @param int $previousPolicyLength
     * @param float $newAmount
     * @param float $previousAmount
     * @return float
     */
    public function getAdjustment($policyLength, $previousPolicyLength, $newAmount, $previousAmount)
    {
        $proRata = (float) $this->getProRata($policyLength, $previousPolicyLength, $newAmount, $previousAmount);
        return round($proRata - $previousAmount, 2);
    }

    /**
     * Gets the amount paid so far at the old rate up to the effective date
     *
     * @param int $previousPolicyLength
     * @param float $previousAmount
     * @return float
     */
    public function getPaidSoFar($previousPolicyLength, $previousAmount)
    {
        $elapsed = $this->getElapsedMonths();
        $paidSoFar = (float) (($previousAmount * $elapsed) / $previousPolicyLength);
        return round($paidSoFar, 2);
    }

    /**
     * Gets the new remainder to pay
     *
     * @param int $newPolicyLength
     * @param float $newAmount
     * @return float
     */
    public function getRemainderToPay($newPolicyLength, $newAmount) {
        $elapsed = $this->getElapsedMonths();
        $remainder = (float) (($newAmount * ($newPolicyLength - $elapsed)) / $newPolicyLength);
        return round($remainder, 2);
    }

    /**
     * Gets the number of months since the policy started
     *
     * @return int
     */
    public function getElapsedMonths()
    {
        $elapsedMonths = (int)$this->effectiveAt->diff($this->startAt)->format('%m');
        $elapsedDays = (int)$this->effectiveAt->diff($this->startAt)->format('%d');
        // Round up so increment if more that one day into the month
        if ($elapsedDays > 0) {
            $elapsedMonths++;
        }
        return $elapsedMonths;
    }

    /**
     * Gets the number of months remaining on the policy
     *
     * @param int $policyLength
     * @return int
     */
    public function getMonthsRemaining($policyLength)
    {
        return $policyLength - self::getElapsedMonths();
    }

}