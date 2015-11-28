<?php

namespace RRP\Transaction;

use RRP\DependencyInjection\LegacyContainer;

/**
 * Class AbstractTransactionDecorator
 *
 * @package RRP\Transaction
 * @author April Portus <april.portus@barbon.com>
 */
abstract class AbstractTransactionDecorator implements TransactionDecoratorInterface
{
    /**
     * @var \RRP\DependencyInjection\LegacyContainer
     */
    protected $container;

    /**
     * @var float
     */
    protected $premium;

    /**
     * @var float
     */
    protected $insuranceNetAmount;

    /**
     * @var int
     */
    protected $iptPercent;

    /**
     * @var int
     */
    protected $termId;

    /**
     * @var int
     */
    protected $mtaId;

    /**
     * @var int
     */
    protected $band;

    /**
     * @var string
     */
    protected $transactionStartDate;

    /**
     * @var string
     */
    protected $policyStartDate;

    /**
     * @var string
     */
    protected $policyIssueDate;

    /**
     * @var \Datasource_Core_Transaction
     */
    protected $transaction;

    /**
     * @var \Datasource_Core_TransactionSupport
     */
    protected $transactionSupport;

    /**
     * @var object
     */
    protected $appData;

    /**
     * @var \Model_Core_Transaction
     */
    protected $transData;

    /**
     * @var \Model_Core_TransactionSupport
     */
    protected $tsData;

    /**
     * @var string containerised constant
     */
    protected $transStatusLive;

    /**
     * @var string containerised constant
     */
    protected $transStatusCancelled;

    /**
     * @var int containerised constant
     */
    protected $tsProductIdRrp;

    /**
     * @var \DateTime
     */
    protected $currentDateAt;

    /**
     * @var float
     */
    protected $transactionAmount;

    /**
     * @inheritdoc
     */
    public function __construct(
        $premium, $insuranceNetAmount, $termId, $mtaId, $band, $transactionStartDate, $appData
    ) {
        $this->container = new LegacyContainer();
        $this->transaction = $this->container->get('rrp.legacy.datasource.transaction');
        $this->transactionSupport = $this->container->get('rrp.legacy.datasource.transaction_support');
        $this->transData = $this->container->get('rrp.legacy.transaction');
        $this->tsData = $this->container->get('rrp.legacy.transaction_support');
        $this->transStatusLive = $this->container->get('rrp.legacy.const.transaction_status_live');
        $this->transStatusCancelled = $this->container->get('rrp.legacy.const.transaction_status_cancelled');
        $this->iptPercent = $this->container->get('rrp.config.ipt_percent');
        $this->premium = $premium;
        $this->insuranceNetAmount = $insuranceNetAmount;
        $this->termId = $termId;
        $this->mtaId = $mtaId;
        $this->band = $band;
        $this->transactionStartDate = $transactionStartDate;
        $this->policyStartDate = $appData->getStartDate();
        $this->policyIssueDate = $appData->getIssueDate();
        $this->appData = $appData;
    }

    /**
     * @inheritdoc
     */
    public function processAllTransactions()
    {
        $this->transactionAmount = 0.0;
        $this->currentDateAt = new \DateTime();

        if ($this->appData->isPayMonthly()) {
            $insuranceNetAmount = $this->getMonthlyShare($this->insuranceNetAmount, $this->appData->getPolicyLength());
            $premium = $this->premium;
            for (
                $paymentNumber = $this->getInitialPaymentNumber();
                $paymentNumber < $this->appData->getPolicyLength();
                $paymentNumber++
            ) {
                $this->processSingleTransaction($premium, $insuranceNetAmount, $paymentNumber);
            }
        }
        else {
            $this->processSingleTransaction($this->premium, $this->insuranceNetAmount, $this->getInitialPaymentNumber());
        }
    }

    /**
     * Calculates the payment date
     *
     * @param int $paymentNumber
     * @return string
     */
    protected function calculatePaymentDate($paymentNumber)
    {
        $transactionDate = $this->policyStartDate;
        if ($paymentNumber > 0) {
            //Ensure the transaction is within the correct month
            $interval = sprintf('P%dM', $paymentNumber);
            $transactionAt = \DateTime::createFromFormat('Y-m-d', $transactionDate);
            $dayOfMonth = $transactionAt->format('d');
            if ($dayOfMonth > 28) {
                $monthNo = $transactionAt->format('m');
                $year = $transactionAt->format('Y');
                $transactionAt->setDate($year, $monthNo, 28);
            }
            $transactionAt->add(new \DateInterval($interval));
            $transactionDate = $transactionAt->format('Y-m-d');
        }
        return $transactionDate;
    }

    /**
     * Gets the payment number for the transaction date
     *
     * @return int
     */
    public function getPaymentNumberForTransactionDate()
    {
        $transactionStartAt = \DateTime::createFromFormat('Y-m-d', $this->transactionStartDate);
        $policyStartAt = \DateTime::createFromFormat('Y-m-d', $this->policyStartDate);

        $elapsedMonths = (int)$transactionStartAt->diff($policyStartAt)->format('%m');
        return $elapsedMonths;
    }

    /**
     * Gets the monthly share amount
     *
     * @param float $amount
     * @param int $policyLength
     * @return float
     */
    private function getMonthlyShare($amount, $policyLength)
    {
        return round($amount / $policyLength, 2);
    }

    /**
     * Creates a transaction
     *
     * @param string $transactionDate
     * @param float $premium
     * @param float $insuranceNetAmount
     * @param int|null $previousId
     * @return bool
     */
    protected function createTransaction($transactionDate, $premium, $insuranceNetAmount, $previousId = null)
    {
        $product = $this->container->get('rrp.legacy.datasource.product');
        $productData = $product->getProductByName($this->appData->getProductName());

        $ipt = round(($premium * $this->iptPercent) / 100.0, 2);
        $this->transData
            ->setId(0)
            ->setPreviousId($previousId)
            ->setEnquiryId(0)
            ->setAmount($premium + $ipt)
            ->setStatusId($this->transStatusLive)
            ->setInsuranceNetAmount($insuranceNetAmount)
            ->setTransactionDate($transactionDate)
            ->setTermId($this->termId)
            ->setMtaId($this->mtaId);
        $transId = $this->transaction->createTransaction($this->transData);

        $this->tsData
            ->setTransId($transId)
            ->setEnquiryId(0)
            ->setProductId($productData->key)
            ->setAgentTypeId(0)
            ->setDealAgentTypeId(0)
            ->setGuarantor(0)
            ->setBand($this->band)
            ->setDuration($this->appData->getPolicyLength())
            ->setRunningAmount($premium + $ipt)
            ->setInsurance($insuranceNetAmount)
            ->setIpt($ipt)
            ->setIncome($premium - $insuranceNetAmount)
            ->setInvoiced(0)
            ->setTransDate($transactionDate);
        if ($previousId) {
            $this->tsData->setStatusChangeDate($this->currentDateAt->format('Y-m-d'));
        }
        $this->transactionAmount += $premium + $ipt;
        return $this->transactionSupport->createTransactionSupport($this->tsData);
    }

    /**
     * Cancels a transaction
     *
     * @param \DateTime $transactionAt
     * @return null|int
     */
    protected function cancelTransaction($transactionAt)
    {
        $monthNo = $transactionAt->format('m');
        $year = $transactionAt->format('Y');

        $this->transData = $this->transaction->getLatestTransactionByTermId($this->termId, $monthNo, $year);
        if ($this->transData) {

            if ($this->transData->getInvoiceID() > 0) {
                $this->transactionAmount += $this->transData->getAmount();
            }

            $this->transData->setStatusId($this->transStatusCancelled);
            $this->transaction->updateTransaction($this->transData);

            $previousId = $this->transData->getId();
            $this->tsData = $this->transactionSupport->getTransactionSupport($previousId);
            $this->tsData
                ->setTransDate($transactionAt->format('Y-m-d'))
                ->setBand($this->band);
            $this->transactionSupport->updateTransactionSupport($this->tsData);

            return $previousId;
        }
        return null;
    }

    /**
     * Gets the transaction amount
     *
     * @return float
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

}