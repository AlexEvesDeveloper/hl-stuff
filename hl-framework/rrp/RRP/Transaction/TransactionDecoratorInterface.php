<?php

namespace RRP\Transaction;

/**
 * Interface TransactionDecoratorInterface
 *
 * @package RRP\Transaction
 * @author April Portus <april.portus@barbon.com>
 */
interface TransactionDecoratorInterface
{
    /**
     * Constructor
     *
     * @param float $premium
     * @param float $insuranceNetAmount
     * @param int $termId
     * @param int $mtaId
     * @param int $band
     * @param string $transactionStartDate
     * @param object $appData
     */
    public function __construct(
        $premium, $insuranceNetAmount, $termId, $mtaId, $band, $transactionStartDate, $appData
    );

    /**
     * Processes all transactions (each month)
     *
     * @return bool
     */
    public function processAllTransactions();

    /**
     * Processes single transaction
     *
     * @param float $premium
     * @param float $insuranceNetAmount
     * @param int $paymentNumber
     * @return bool
     */
    public function processSingleTransaction($premium, $insuranceNetAmount, $paymentNumber);

    /**
     * Calculates the Initial Payment Number starting at zero
     *
     * @return int
     */
    public function getInitialPaymentNumber();

}
