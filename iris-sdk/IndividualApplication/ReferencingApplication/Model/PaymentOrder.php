<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class PaymentOrder
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class PaymentOrder extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var string
     */
    private $paymentPortalStartUrl;

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
     * get payment portal start url
     *
     * @return string
     */
    public function getPaymentPortalStartUrl()
    {
        return $this->paymentPortalStartUrl;
    }

    /**
     * Set payment portal start url
     *
     * @param string $paymentPortalStartUrl
     */
    public function setPaymentPortalStartUrl($paymentPortalStartUrl)
    {
        $this->paymentPortalStartUrl = $paymentPortalStartUrl;
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
