<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

/**
 * @Iris\Entity\Product
 */
class Product implements JsonSerializable
{
    /**
     * @Iris\Field
     * @var int
     */
    private $productId;

    /**
     * @Iris\Field
     * @var string
     */
    private $name;

    /**
     * @Iris\Field
     * @var string
     */
    private $description;

    /**
     * @Iris\Field
     * @var string
     */
    private $productCode;

    /**
     * @Iris\Field(accessor = "hasRentGuarantee")
     * @var boolean
     */
    private $hasRentGuarantee;

    /**
     * @Iris\Field(accessor = "hasFinancialReference")
     * @var boolean
     */
    private $hasFinancialReference;

    /**
     * @Iris\Field(accessor = "hasLettingReference")
     * @var boolean
     */
    private $hasLettingReference;

    /**
     * @Iris\Field(accessor = "hasUnknownResidentialStatus")
     * @var boolean
     */
    private $hasUnknownResidentialStatus;

    /**
     * @Iris\Field(accessor = "hasUnknownEmploymentStatus")
     * @var boolean
     */
    private $hasUnknownEmploymentStatus;

    /**
     * @Iris\Field(accessor = "isInternational", mutator = "setIsInternational")
     * @var boolean
     */
    private $international;

    
    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set product ID
     *
     * @param int $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get product code
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set product code
     *
     * @param string $productCode
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }

    /**
     * Has rent guarantee
     *
     * @return boolean
     */
    public function hasRentGuarantee()
    {
        return $this->hasRentGuarantee;
    }

    /**
     * Set has rent guarantee
     *
     * @param boolean $hasRentGuarantee
     */
    public function setHasRentGuarantee($hasRentGuarantee)
    {
        $this->hasRentGuarantee = $hasRentGuarantee;
    }

    /**
     * Has financial reference
     *
     * @return boolean
     */
    public function hasFinancialReference()
    {
        return $this->hasFinancialReference;
    }

    /**
     * Set has financial reference
     *
     * @param boolean $hasFinancialReference
     */
    public function setHasFinancialReference($hasFinancialReference)
    {
        $this->hasFinancialReference = $hasFinancialReference;
    }

    /**
     * Has letting reference
     *
     * @return boolean
     */
    public function hasLettingReference()
    {
        return $this->hasLettingReference;
    }

    /**
     * Set has letting reference
     *
     * @param boolean $hasLettingReference
     */
    public function setHasLettingReference($hasLettingReference)
    {
        $this->hasLettingReference = $hasLettingReference;
    }

    /**
     * Has unknown residential status
     *
     * @return boolean
     */
    public function hasUnknownResidentialStatus()
    {
        return $this->hasUnknownResidentialStatus;
    }

    /**
     * Set has unknown residential status
     *
     * @param boolean $hasUnknownResidentialStatus
     */
    public function setHasUnknownResidentialStatus($hasUnknownResidentialStatus)
    {
        $this->hasUnknownResidentialStatus = $hasUnknownResidentialStatus;
    }

    /**
     * Has unknown employment status
     *
     * @return boolean
     */
    public function hasUnknownEmploymentStatus()
    {
        return $this->hasUnknownEmploymentStatus;
    }

    /**
     * Set has unknown employment status
     *
     * @param boolean $hasUnknownEmploymentStatus
     */
    public function setHasUnknownEmploymentStatus($hasUnknownEmploymentStatus)
    {
        $this->hasUnknownEmploymentStatus = $hasUnknownEmploymentStatus;
    }

    /**
     * Is international
     *
     * @return boolean
     */
    public function isInternational()
    {
        return $this->international;
    }

    /**
     * Set is international
     *
     * @param boolean $international
     */
    public function setIsInternational($international)
    {
        $this->international = $international;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'productId' => $this->getProductId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'productCode' => $this->getProductCode(),
            'hasRentGuarantee' => $this->hasRentGuarantee(),
            'hasFinancialReference' => $this->hasFinancialReference(),
            'hasLettingReference' => $this->hasLettingReference(),
            'hasUnknownResidentialStatus' => $this->hasUnknownResidentialStatus(),
            'hasUnknownEmploymentStatus' => $this->hasUnknownEmploymentStatus(),
            'international' => $this->isInternational()
        ];
    }
}
