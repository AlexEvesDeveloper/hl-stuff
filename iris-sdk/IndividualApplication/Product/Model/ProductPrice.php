<?php

namespace Barbondev\IRISSDK\IndividualApplication\Product\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class ProductPrice
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Product\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ProductPrice extends AbstractResponseModel
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $policyLength;

    /**
     * @var float
     */
    private $grossPrice;

    /**
     * @var float
     */
    private $vatAmount;

    /**
     * @var float
     */
    private $netPrice;

    /**
     * @var float
     */
    private $insuranceGrossPrice;

    /**
     * @var float
     */
    private $insurancePremiumTaxAmount;

    /**
     * @var float
     */
    private $insuranceNetPrice;

    /**
     * Create a response model object from a completed command
     *
     * @param OperationCommand $command That serialized the request
     *
     * @return self
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        return self::hydrateModelProperties(
            new self(),
            $data
        );
    }

    /**
     * Set productId
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Get productId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set grossPrice
     *
     * @param float $grossPrice
     * @return $this
     */
    public function setGrossPrice($grossPrice)
    {
        $this->grossPrice = $grossPrice;
        return $this;
    }

    /**
     * Get grossPrice
     *
     * @return float
     */
    public function getGrossPrice()
    {
        return $this->grossPrice;
    }

    /**
     * Set insuranceGrossPrice
     *
     * @param float $insuranceGrossPrice
     * @return $this
     */
    public function setInsuranceGrossPrice($insuranceGrossPrice)
    {
        $this->insuranceGrossPrice = $insuranceGrossPrice;
        return $this;
    }

    /**
     * Get insuranceGrossPrice
     *
     * @return float
     */
    public function getInsuranceGrossPrice()
    {
        return $this->insuranceGrossPrice;
    }

    /**
     * Set insuranceNetPrice
     *
     * @param float $insuranceNetPrice
     * @return $this
     */
    public function setInsuranceNetPrice($insuranceNetPrice)
    {
        $this->insuranceNetPrice = $insuranceNetPrice;
        return $this;
    }

    /**
     * Get insuranceNetPrice
     *
     * @return float
     */
    public function getInsuranceNetPrice()
    {
        return $this->insuranceNetPrice;
    }

    /**
     * Set insurancePremiumTaxAmount
     *
     * @param float $insurancePremiumTaxAmount
     * @return $this
     */
    public function setInsurancePremiumTaxAmount($insurancePremiumTaxAmount)
    {
        $this->insurancePremiumTaxAmount = $insurancePremiumTaxAmount;
        return $this;
    }

    /**
     * Get insurancePremiumTaxAmount
     *
     * @return float
     */
    public function getInsurancePremiumTaxAmount()
    {
        return $this->insurancePremiumTaxAmount;
    }

    /**
     * Set netPrice
     *
     * @param float $netPrice
     * @return $this
     */
    public function setNetPrice($netPrice)
    {
        $this->netPrice = $netPrice;
        return $this;
    }

    /**
     * Get netPrice
     *
     * @return float
     */
    public function getNetPrice()
    {
        return $this->netPrice;
    }

    /**
     * Set policyLength
     *
     * @param int $policyLength
     * @return $this
     */
    public function setPolicyLength($policyLength)
    {
        $this->policyLength = $policyLength;
        return $this;
    }

    /**
     * Get policyLength
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Set vatAmount
     *
     * @param float $vatAmount
     * @return $this
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
        return $this;
    }

    /**
     * Get vatAmount
     *
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }
}
