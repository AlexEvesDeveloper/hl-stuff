<?php

namespace RRP\Application\Decorators;

use RRP\Application\AbstractApplicationDecorator;
use RRP\Application\ApplicationDecoratorInterface;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Transaction\TransactionDecoratorFactory;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class RentRecoveryPlusPolicy
 *
 * @package RRP\Application\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusPolicy extends AbstractApplicationDecorator implements ApplicationDecoratorInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct('rrp.legacy.datasource.policy');
    }

    /**
     * Cancel the policy
     *
     * @param string $policyEndDate
     * @return null|float
     */
    public function cancel($policyEndDate)
    {
        $cancelledDate = date('Y-m-d');
        $this->appData->setCancelledDate($cancelledDate);
        $this->appData->setEndDate($policyEndDate);
        $this->appData->setPayStatus($this->container->get('rrp.legacy.const.pay_status_policy'));

        $propertyRental = PolicyOptionsManager::getOption(
            $this->appData->getPolicyOptions(),
            $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
            $this->appData->getAmountscovered()
        );

        $termData = $this->policyTerm->getPolicyTerm(
            $this->appData->getPolicyNumber(),
            $this->appData->getStartDate()
        );
        if ($termData) {
            if ($this->application->save($this->appData)) {
                if ($propertyRental < RentRecoveryPlus::BAND_A_LIMIT) {
                    $transBand = RentRecoveryPlus::BAND_A;
                } else {
                    $transBand = RentRecoveryPlus::BAND_B;
                }
                $cancellationPeriod = $this->container->get('rrp.config.cancellation_period');
                if ($termData->term > 1) {
                    $refundEndAt = \DateTime::createFromFormat('Y-m-d', $this->appData->getStartDate());
                }
                else {
                    $refundEndAt = \DateTime::createFromFormat('Y-m-d', $this->appData->getIssueDate());
                }
                $refundEndAt = $refundEndAt->setTime(23, 59, 59)->add(new \DateInterval($cancellationPeriod));
                $policyEndAt =  \DateTime::createFromFormat('Y-m-d', $policyEndDate);
                $refundValue = 0.0;
                $cancelFromDate = null;
                if ($policyEndAt < $refundEndAt) {
                    $cancelFromDate = $this->appData->getIssueDate();
                }
                else {
                    $cancelFromDate = $policyEndDate;
                }
                if ($cancelFromDate !== null) {
                    $transaction = TransactionDecoratorFactory::getDecorator(
                        TransactionDecoratorFactory::TRANSACTION_CANCEL,
                        0,
                        0,
                        $termData->id,
                        0,
                        $transBand,
                        $cancelFromDate,
                        $this->appData
                    );
                    $transaction->processAllTransactions();
                    $refundValue = $transaction->getTransactionAmount();
                }
                return $refundValue;
            }
        }
        return null;
    }
}