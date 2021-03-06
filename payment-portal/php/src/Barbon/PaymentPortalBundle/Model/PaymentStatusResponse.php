<?php

namespace Barbon\PaymentPortalBundle\Model;

use Barbon\PaymentPortalBundle\Entity\Payer;

/**
 * Class PaymentStatusResponse
 *
 * @package Barbon\PaymentPortalBundle\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class PaymentStatusResponse
{
    /**
     * @var string
     */
    private $transactionUuId;

    /**
     * @var string
     */
    private $orderUuId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var OrderStatus
     */
    private $paymentStatus;

    /**
     * @var string
     */
    private $processor;

    /**
     * @var int
     */
    private $paymentType;

    /**
     * @var Payer
     */
    private $payer;

    /**
     * @var array
     */
    private $payload;

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get orderUuId
     *
     * @return string
     */
    public function getOrderUuId()
    {
        return $this->orderUuId;
    }

    /**
     * Set orderUuId
     *
     * @param string $orderUuId
     * @return $this
     */
    public function setOrderUuId($orderUuId)
    {
        $this->orderUuId = $orderUuId;
        return $this;
    }

    /**
     * Get payer
     *
     * @return Payer
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set payer
     *
     * @param Payer $payer
     * @return $this
     */
    public function setPayer(Payer $payer = null)
    {
        $this->payer = $payer;
        return $this;
    }

    /**
     * Get payload
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set payload
     *
     * @param array $payload
     * @return $this
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return OrderStatus
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set paymentStatus
     *
     * @param OrderStatus $paymentStatus
     * @return $this
     */
    public function setPaymentStatus(OrderStatus $paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * Get paymentType
     *
     * @return int
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set paymentType
     *
     * @param int $paymentType
     * @return $this
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * Get processor
     *
     * @return string
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set processor
     *
     * @param string $processor
     * @return $this
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * Get transactionUuId
     *
     * @return string
     */
    public function getTransactionUuId()
    {
        return $this->transactionUuId;
    }

    /**
     * Set transactionUuId
     *
     * @param string $transactionUuId
     * @return $this
     */
    public function setTransactionUuId($transactionUuId)
    {
        $this->transactionUuId = $transactionUuId;
        return $this;
    }
}