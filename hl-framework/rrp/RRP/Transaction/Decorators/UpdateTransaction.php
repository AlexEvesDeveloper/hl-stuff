<?php
namespace RRP\Transaction\Decorators;

use RRP\Transaction\AbstractTransactionDecorator;
use RRP\Transaction\TransactionDecoratorInterface;

/**
 * Class UpdateTransaction
 *
 * @package RRP\Transaction\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class UpdateTransaction extends AbstractTransactionDecorator implements TransactionDecoratorInterface
{
    /**
     * @inheritdoc
     */
    public function processSingleTransaction($premium, $insuranceNetAmount, $paymentNumber)
    {
        $transactionDate = $this->calculatePaymentDate($paymentNumber);

        $transactionAt = \DateTime::createFromFormat('Y-m-d', $transactionDate);

        //Cancel the previous transaction
        $previousId = $this->cancelTransaction($transactionAt);

        //Create a new one
        return $this->createTransaction($transactionDate, $premium, $insuranceNetAmount, $previousId);
    }

    /**
     * @inheritdoc
     */
    public function getInitialPaymentNumber()
    {
        return $this->getPaymentNumberForTransactionDate();
    }

}
