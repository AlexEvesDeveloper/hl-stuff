<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\ProductPrice
 */
class ProductPrice
{
    /**
     * @Iris\Field
     * @var int
     */
    private $productId;

    /**
     * @Iris\Field
     * @var int
     */
    private $policyLength;

    /**
     * @Iris\Field
     * @var int
     */
    private $grossPrice;

    /**
     * @Iris\Field
     * @var int
     */
    private $vatAmount;

    /**
     * @Iris\Field
     * @var int
     */
    private $netPrice;

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * @param int $policyLength
     */
    public function setPolicyLength($policyLength)
    {
        $this->policyLength = $policyLength;
    }

    /**
     * @return int
     */
    public function getGrossPrice()
    {
        return $this->grossPrice;
    }

    /**
     * @param int $grossPrice
     */
    public function setGrossPrice($grossPrice)
    {
        $this->grossPrice = $grossPrice;
    }

    /**
     * @return int
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param int $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }

    /**
     * @return int
     */
    public function getNetPrice()
    {
        return $this->netPrice;
    }

    /**
     * @param int $netPrice
     */
    public function setNetPrice($netPrice)
    {
        $this->netPrice = $netPrice;
    }
}
