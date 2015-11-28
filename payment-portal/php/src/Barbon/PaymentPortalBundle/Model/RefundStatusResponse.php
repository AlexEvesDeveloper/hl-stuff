<?php

namespace Barbon\PaymentPortalBundle\Model;

use Barbon\PaymentPortalBundle\Entity\Payer;

/**
 * Class RefundStatusResponse
 *
 * @package Barbon\PaymentPortalBundle\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RefundStatusResponse
{
    /**
     * @var string
     */
    private $originalTransactionUuId;

    /**
     * @var string
     */
    private $refundTransactionUuId;

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
    private $refundStatus;

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
     * Get refundStatus
     *
     * @return OrderStatus
     */
    public function getRefundStatus()
    {
        return $this->refundStatus;
    }

    /**
     * Set refundStatus
     *
     * @param OrderStatus $refundStatus
     * @return $this
     */
    public function setRefundStatus(OrderStatus $refundStatus)
    {
        $this->refundStatus = $refundStatus;
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
     * Get originalTransactionUuId
     *
     * @return string
     */
    public function getOriginalTransactionUuId()
    {
        return $this->originalTransactionUuId;
    }

    /**
     * Set originalTransactionUuId
     *
     * @param string $originalTransactionUuId
     * @return $this
     */
    public function setOriginalTransactionUuId($originalTransactionUuId)
    {
        $this->originalTransactionUuId = $originalTransactionUuId;
        return $this;
    }

    /**
     * Get refundTransactionUuId
     *
     * @return string
     */
    public function getRefundTransactionUuId()
    {
        return $this->refundTransactionUuId;
    }

    /**
     * Set refundTransactionUuId
     *
     * @param string $refundTransactionUuId
     * @return $this
     */
    public function setRefundTransactionUuId($refundTransactionUuId)
    {
        $this->refundTransactionUuId = $refundTransactionUuId;
        return $this;
    }
}