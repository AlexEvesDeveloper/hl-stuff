<?php
namespace RRP\Transaction\Decorators;

use RRP\Transaction\AbstractTransactionDecorator;
use RRP\Transaction\TransactionDecoratorInterface;

/**
 * Class CancelTransaction
 *
 * @package RRP\Transaction\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class CancelTransaction extends AbstractTransactionDecorator implements TransactionDecoratorInterface
{
    /**
     * @inheritdoc
     */
    public function processSingleTransaction($premium, $insuranceNetAmount, $paymentNumber)
    {
        $transactionDate = $this->calculatePaymentDate($paymentNumber);

        $transactionAt = \DateTime::createFromFormat('Y-m-d', $transactionDate);
        // Check $transactionAt date later than end of current month

        //Cancel the previous transaction
        if ($this->cancelTransaction($transactionAt)) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getInitialPaymentNumber()
    {
        $isFullRefund = ($this->transactionStartDate == $this->policyIssueDate);
        if ($isFullRefund) {
            return 0;
        }
        return 1 + $this->getPaymentNumberForTransactionDate();
    }
}
