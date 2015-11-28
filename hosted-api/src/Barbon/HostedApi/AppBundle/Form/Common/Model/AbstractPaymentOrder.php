<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

class AbstractPaymentOrder
{
    /**
     * @Iris\Id
     * @Iris\Field
     * @var string
     */
    private $applicationId;

    /**
     * @Iris\Field
     * @var string
     */
    private $paymentPortalStartUrl;

    /**
     * @Iris\Field
     * @var array
     */
    private $paymentTypes;

    /**
     * @Iris\Field
     * @var string
     */
    private $redirectOnSuccessUrl;

    /**
     * @Iris\Field
     * @var string
     */
    private $redirectOnFailureUrl;

    /**
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param int $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return string
     */
    public function getPaymentPortalStartUrl()
    {
        return $this->paymentPortalStartUrl;
    }

    /**
     * @param string $paymentPortalStartUrl
     */
    public function setPaymentPortalStartUrl($paymentPortalStartUrl)
    {
        $this->paymentPortalStartUrl = $paymentPortalStartUrl;
    }

    /**
     * @return array
     */
    public function getPaymentTypes()
    {
        return $this->paymentTypes;
    }

    /**
     * @param array $paymentTypes
     */
    public function setPaymentTypes($paymentTypes)
    {
        $this->paymentTypes = $paymentTypes;
    }

    /**
     * @return string
     */
    public function getRedirectOnSuccessUrl()
    {
        return $this->redirectOnSuccessUrl;
    }

    /**
     * @param string $redirectOnSuccessUrl
     */
    public function setRedirectOnSuccessUrl($redirectOnSuccessUrl)
    {
        $this->redirectOnSuccessUrl = $redirectOnSuccessUrl;
    }

    /**
     * @return string
     */
    public function getRedirectOnFailureUrl()
    {
        return $this->redirectOnFailureUrl;
    }

    /**
     * @param string $redirectOnFailureUrl
     */
    public function setRedirectOnFailureUrl($redirectOnFailureUrl)
    {
        $this->redirectOnFailureUrl = $redirectOnFailureUrl;
    }
}
