<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class PaymentStatus
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class PaymentStatus extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var int
     */
    private $paymentStatus;

    /**
     * Get application id
     *
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set application id
     *
     * @param string $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Get payment status
     *
     * @return int
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Get payment status
     *
     * @param int $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();
        return self::hydrateModelProperties(new self(), $data);
    }
}
