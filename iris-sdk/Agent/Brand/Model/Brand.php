<?php

namespace Barbondev\IRISSDK\Agent\Brand\Model;

use Barbondev\IRISSDK\Common\Model\Address;

/**
 * Class Brand
 *
 * @package Barbondev\IRISSDK\Agent\Brand\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Brand
{
    /**
     * @var string
     */
    private $brandName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $websiteUrl;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var array
     */
    private $options;

    /**
     * Set address
     *
     * @param \Barbondev\IRISSDK\Common\Model\Address $address
     * @return $this
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Barbondev\IRISSDK\Common\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
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
     * Get brandName
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
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
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
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
     * Get options - gets JSON decoded
     *
     * @return array
     */
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * Set options - gets JSON encoded
     *
     * @param array|string $options
     * @return $this
     */
    public function setOptions($options)
    {
        // The AbstractResponseModel::hydrateModelProperties() method calls model setters using incoming data as-is, in
        // this case a string, so this setter must be able to deal with mixed type input.
        if (is_array($options)) {
            $this->options = json_encode($options);
        }
        else {
            $this->options = $options;
        }

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
}