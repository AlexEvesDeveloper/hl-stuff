<?php

namespace Barbon\PaymentPortalBundle\Model;

/**
 * Class OrderStatus
 *
 * @package Barbon\PaymentPortalBundle\Model
 * @author Ashley Dawson <ashley>
 */
class OrderStatus
{
    /**
     * Identifier for a successful order
     */
    const STATUS_SUCCESS = 1;

    /**
     * Identifier for a failed order
     */
    const STATUS_FAILURE = 2;

    /**
     * Identifier for a pending order
     */
    const STATUS_PENDING = 3;

    /**
     * Identifier code for captured payments
     */
    const CODE_PAYMENT_CAPTURED = 100;

    /**
     * Identifier code where the payment capture failed
     */
    const CODE_PAYMENT_FAILED_TO_CAPTURE = 101;

    /**
     * Identifier code where the payment failed the 3D secure authorization
     */
    const CODE_PAYMENT_3D_SECURE_FAILURE = 102; #todo now matches documentation but still requires implementing

    /**
     * Identifier code where a refund was generated
     */
    const CODE_REFUND_GENERATED = 200;

    /**
     * Identifier code for failed refunds
     */
    const CODE_REFUND_FAILED = 201;

    /**
     * Identifier code for captured payments
     */
    const CODE_REPEAT_PAYMENT_CAPTURED = 300;

    /**
     * Identifier code where the payment capture failed
     */
    const CODE_REPEAT_PAYMENT_FAILED_TO_CAPTURE = 301;

    /**
     * @var int
     */
    private $status;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * Get code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}