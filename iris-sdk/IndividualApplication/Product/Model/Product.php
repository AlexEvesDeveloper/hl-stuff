<?php

namespace Barbondev\IRISSDK\IndividualApplication\Product\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class Product
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Product\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Product extends AbstractResponseModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $hasRentGuarantee;

    /**
     * @var string
     */
    public $productCode;

    /**
     * @var bool
     */
    public $hasFinancialReference;

    /**
     * @var bool
     */
    public $hasLettingReference;

    /**
     * @var bool
     */
    public $hasUnknownResidentialStatus;

    /**
     * @var bool
     */
    public $hasUnknownEmploymentStatus;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        // If there is no content then assume an empty collection
        if (204 == $command->getResponse()->getStatusCode()) {
            return new Collection();
        }

        $data = $command->getResponse()->json();

        // Collection of products
        if (self::isResponseDataIndexedArray($data)) {

            $products = new Collection();

            foreach ($data as $key => $productData) {
                $products->add($key, self::hydrateModelProperties(
                    new self(),
                    $productData,
                    array(
                        'productId' => 'id',
                    )
                ));
            }

            return $products;
        }

        // Single product
        else {

            return self::hydrateModelProperties(
                new self(),
                $data,
                array(
                    'productId' => 'id',
                )
            );
        }
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * Set hasRentGuarantee
     *
     * @param boolean $hasRentGuarantee
     * @return $this
     */
    public function setHasRentGuarantee($hasRentGuarantee)
    {
        $this->hasRentGuarantee = $hasRentGuarantee;
        return $this;
    }

    /**
     * Get hasRentGuarantee
     *
     * @return boolean
     */
    public function getHasRentGuarantee()
    {
        return $this->hasRentGuarantee;
    }

    /**
     * Set hasFinancialReference
     *
     * @param boolean $hasFinancialReference
     * @return $this
     */
    public function setHasFinancialReference($hasFinancialReference)
    {
        $this->hasFinancialReference = $hasFinancialReference;
        return $this;
    }

    /**
     * Get hasFinancialReference
     *
     * @return boolean
     */
    public function getHasFinancialReference()
    {
        return $this->hasFinancialReference;
    }

    /**
     * Set hasLettingReference
     *
     * @param boolean $hasLettingReference
     * @return $this
     */
    public function setHasLettingReference($hasLettingReference)
    {
        $this->hasLettingReference = $hasLettingReference;
        return $this;
    }

    /**
     * Get hasLettingReference
     *
     * @return boolean
     */
    public function getHasLettingReference()
    {
        return $this->hasLettingReference;
    }

    /**
     * Set hasUnknownEmploymentStatus
     *
     * @param boolean $hasUnknownEmploymentStatus
     * @return $this
     */
    public function setHasUnknownEmploymentStatus($hasUnknownEmploymentStatus)
    {
        $this->hasUnknownEmploymentStatus = $hasUnknownEmploymentStatus;
        return $this;
    }

    /**
     * Get hasUnknownEmploymentStatus
     *
     * @return boolean
     */
    public function getHasUnknownEmploymentStatus()
    {
        return $this->hasUnknownEmploymentStatus;
    }

    /**
     * Set hasUnknownResidentialStatus
     *
     * @param boolean $hasUnknownResidentialStatus
     * @return $this
     */
    public function setHasUnknownResidentialStatus($hasUnknownResidentialStatus)
    {
        $this->hasUnknownResidentialStatus = $hasUnknownResidentialStatus;
        return $this;
    }

    /**
     * Get hasUnknownResidentialStatus
     *
     * @return boolean
     */
    public function getHasUnknownResidentialStatus()
    {
        return $this->hasUnknownResidentialStatus;
    }

    /**
     * Set productCode
     *
     * @param string $productCode
     * @return $this
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
        return $this;
    }

    /**
     * Get productCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }
}