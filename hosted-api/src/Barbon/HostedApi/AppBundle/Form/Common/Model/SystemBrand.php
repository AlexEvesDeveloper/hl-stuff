<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\SystemBrand
 */
class SystemBrand
{
    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $brandName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $email;

    /**
     * @Iris\Field(optional = true)
     * @var Address
     */
    private $address;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $phone;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $fax;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $websiteUrl;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $logo;

    /**
     * @Iris\Field(optional = true, format = "json")
     * @var BrandOptions
     */
    private $options;

    /**
     * Get brandName
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * Set brandName
     *
     * @param string $brandName
     * @return $this
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param Address $address
     * @return $this
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * Get websiteUrl
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return $this
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get options
     *
     * @return BrandOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options
     *
     * @param BrandOptions $options
     * @return $this
     */
    public function setOptions(BrandOptions $options)
    {
        $this->options = $options;
        return $this;
    }
}