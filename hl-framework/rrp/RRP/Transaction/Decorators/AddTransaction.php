<?php
namespace RRP\Transaction\Decorators;

use RRP\Transaction\AbstractTransactionDecorator;
use RRP\Transaction\TransactionDecoratorInterface;

/**
 * Class AddTransaction
 *
 * @package RRP\Transaction\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class AddTransaction extends AbstractTransactionDecorator implements TransactionDecoratorInterface
{
    /**
     * @inheritdoc
     */
    public function processSingleTransaction($premium, $insuranceNetAmount, $paymentNumber)
    {
        $transactionDate = $this->calculatePaymentDate($paymentNumber);

        return $this->createTransaction($transactionDate, $premium, $insuranceNetAmount);
    }

    /**
     * @inheritdoc
     */
    public function getInitialPaymentNumber()
    {
        return 0;
    }

}
