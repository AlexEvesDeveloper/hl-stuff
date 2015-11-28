<?php

namespace RRP\Mta;

/**
 * Interface MtaDecoratorInterface
 *
 * @package RRP\Mta
 * @author April Portus <april.portus@barbon.com>
 */
interface MtaDecoratorInterface
{
    /**
     * Constructor
     */
    public function __construct();

    /**
     * Creates an MTA for the RRP policy
     *
     * @param \RRP\Application\Decorators\RentRecoveryPlusPolicy $policy
     * @param \RRP\Model\RentRecoveryPlusMta $mta
     * @param float $premium
     * @param float $quote
     * @param float $ipt
     * @param float $nilExcessOption
     * @return bool
     */
    public function create($policy, $mta, $premium, $quote, $ipt, $nilExcessOption);

    /**
     * Accept the MTA - make is live and copy to the policy
     *
     * @param string $policyNumber
     * @param int $mtaId
     * @return bool
     */
    public function accept($policyNumber, $mtaId);
}