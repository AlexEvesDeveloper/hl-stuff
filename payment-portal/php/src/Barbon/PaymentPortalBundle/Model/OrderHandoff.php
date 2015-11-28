<?php

namespace Barbon\PaymentPortalBundle\Model;

/**
 * Class OrderHandoff
 *
 * @package Barbon\PaymentPortalBundle\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class OrderHandoff
{
    /**
     * @var string
     */
    private $paymentPortalStartUrl;

    /**
     * @var string
     */
    private $orderUuId;

    /**
     * Constructor
     *
     * @param string $orderUuId
     * @param string $paymentPortalStartUrl
     */
    public function __construct($orderUuId, $paymentPortalStartUrl)
    {
        $this->orderUuId = $orderUuId;
        $this->paymentPortalStartUrl = $paymentPortalStartUrl;
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
     * Get paymentPortalStartUrl
     *
     * @return string
     */
    public function getPaymentPortalStartUrl()
    {
        return $this->paymentPortalStartUrl;
    }

    /**
     * Set paymentPortalStartUrl
     *
     * @param string $paymentPortalStartUrl
     * @return $this
     */
    public function setPaymentPortalStartUrl($paymentPortalStartUrl)
    {
        $this->paymentPortalStartUrl = $paymentPortalStartUrl;
        return $this;
    }
}