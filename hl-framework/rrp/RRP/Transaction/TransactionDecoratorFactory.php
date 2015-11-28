<?php

namespace RRP\Transaction;

use RRP\DependencyInjection\LegacyContainer;
use RRP\Transaction\Exception\UnknownTransactionTypeException;

/**
 * Class TransactionDecoratorFactory
 *
 * @package RRP\Transaction
 * @author April Portus <april.portus@barbon.com>
 */
class TransactionDecoratorFactory
{
    /**
     * Add Transaction decorator type
     */
    const TRANSACTION_ADD = 'AddTransaction';

    /**
     * Update Transaction decorator type
     */
    const TRANSACTION_UPDATE = 'UpdateTransaction';

    /**
     * Cancel Transaction decorator type
     */
    const TRANSACTION_CANCEL = 'CancelTransaction';

    /**
     * Gets the transaction decorator
     *
     * @param string $transactionType
     * @param float $premium
     * @param float $insuranceNetAmount
     * @param int $termId
     * @param int $mtaId
     * @param int $band
     * @param string $transactionStartDate
     * @param object $appData
     * @return object
     * @throws Exception\UnknownTransactionTypeException
     */
    public static function getDecorator(
        $transactionType, $premium, $insuranceNetAmount, $termId, $mtaId, $band, $transactionStartDate, $appData
    ) {
        $className = 'RRP\Transaction\Decorators\\' . $transactionType;

        switch ($transactionType) {
            case self::TRANSACTION_ADD:
            case self::TRANSACTION_UPDATE:
            case self::TRANSACTION_CANCEL:
                return new $className(
                    $premium,
                    $insuranceNetAmount,
                    $termId,
                    $mtaId,
                    $band,
                    $transactionStartDate,
                    $appData
                );

            default:
                throw new UnknownTransactionTypeException();
        }
    }

}