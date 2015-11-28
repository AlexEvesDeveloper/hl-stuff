<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use DateTime;

/**
 * @Iris\Entity\AgentBrandBranch
 */
class AgentBrandBranch
{
    /**
     * @Iris\Field
     * @var string
     */
    private $agentSchemeNumber;

    /**
     * @Iris\Field
     * @var string
     */
    private $name;

    /**
     * @Iris\Field
     * @var Address
     */
    private $address;

    /**
     * @Iris\Field(optional = true)
     * @var Address
     */
    private $correspondenceAddress;

    /**
     * @Iris\Field
     * @var string
     */
    private $contactName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $email;

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
     * @Iris\Field
     * @var string
     */
    private $status;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $websiteUrl;

    /**
     * @Iris\Field(format = "Y-m-d",optional = true)
     * @var DateTime
     */
    private $createdAt;

    /**
     * @Iris\Field(accessor = "hasSMS",optional = true)
     * @var boolean
     */
    private $hasSMS;

    /**
     * @Iris\Field(accessor = "hasMailer",optional = true)
     * @var boolean
     */
    private $hasMailer;

    /**
     * @Iris\Field(accessor = "isPremier",optional = true)
     * @var boolean
     */
    private $isPremier;

    /**
     * @Iris\Field(accessor = "hasTenantMailerOptin",optional = true)
     * @var boolean
     */
    private $hasTenantMailerOptin;

    /**
     * @Iris\Field(accessor = "hasInterimReport",optional = true)
     * @var boolean
     */
    private $hasInterimReport;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $source;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $generalEmail;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $referencingEmail;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $insuranceEmail;

    /**
     * @Iris\Field(optional = true)
     * @var AgentBrand
     */
    private $agentBrand;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $doNotContactTenant;

    /**
     * Get agentSchemeNumber
     *
     * @return string
     */
    public function getAgentSchemeNumber()
    {
        return $this->agentSchemeNumber;
    }

    /**
     * Set agentSchemeNumber
     *
     * @param string $agentSchemeNumber
     * @return $this
     */
    public function setAgentSchemeNumber($agentSchemeNumber)
    {
        $this->agentSchemeNumber = $agentSchemeNumber;
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
     * Get correspondenceAddress
     *
     * @return Address
     */
    public function getCorrespondenceAddress()
    {
        return $this->correspondenceAddress;
    }

    /**
     * Set correspondenceAddress
     *
     * @param Address $correspondenceAddress
     * @return $this
     */
    public function setCorrespondenceAddress(Address $correspondenceAddress)
    {
        $this->correspondenceAddress = $correspondenceAddress;
        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return $this
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get hasSMS
     *
     * @return boolean
     */
    public function hasSMS()
    {
        return $this->hasSMS;
    }

    /**
     * Set hasSMS
     *
     * @param boolean $hasSMS
     * @return $this
     */
    public function setHasSMS($hasSMS)
    {
        $this->hasSMS = $hasSMS;
        return $this;
    }

    /**
     * Get hasMailer
     *
     * @return boolean
     */
    public function hasMailer()
    {
        return $this->hasMailer;
    }

    /**
     * Set hasMailer
     *
     * @param boolean $hasMailer
     * @return $this
     */
    public function setHasMailer($hasMailer)
    {
        $this->hasMailer = $hasMailer;
        return $this;
    }

    /**
     * Get isPremier
     *
     * @return boolean
     */
    public function isPremier()
    {
        return $this->isPremier;
    }

    /**
     * Set isPremier
     *
     * @param boolean $isPremier
     * @return $this
     */
    public function setIsPremier($isPremier)
    {
        $this->isPremier = $isPremier;
        return $this;
    }

    /**
     * Get hasTenantMailerOptin
     *
     * @return boolean
     */
    public function hasTenantMailerOptin()
    {
        return $this->hasTenantMailerOptin;
    }

    /**
     * Set hasTenantMailerOptin
     *
     * @param boolean $hasTenantMailerOptin
     * @return $this
     */
    public function setHasTenantMailerOptin($hasTenantMailerOptin)
    {
        $this->hasTenantMailerOptin = $hasTenantMailerOptin;
        return $this;
    }

    /**
     * Get hasInterimReport
     *
     * @return boolean
     */
    public function hasInterimReport()
    {
        return $this->hasInterimReport;
    }

    /**
     * Set hasInterimReport
     *
     * @param boolean $hasInterimReport
     * @return $this
     */
    public function setHasInterimReport($hasInterimReport)
    {
        $this->hasInterimReport = $hasInterimReport;
        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get generalEmail
     *
     * @return string
     */
    public function getGeneralEmail()
    {
        return $this->generalEmail;
    }

    /**
     * Set generalEmail
     *
     * @param string $generalEmail
     * @return $this
     */
    public function setGeneralEmail($generalEmail)
    {
        $this->generalEmail = $generalEmail;
        return $this;
    }

    /**
     * Get referencingEmail
     *
     * @return string
     */
    public function getReferencingEmail()
    {
        return $this->referencingEmail;
    }

    /**
     * Set referencingEmail
     *
     * @param string $referencingEmail
     * @return $this
     */
    public function setReferencingEmail($referencingEmail)
    {
        $this->referencingEmail = $referencingEmail;
        return $this;
    }

    /**
     * Get insuranceEmail
     *
     * @return string
     */
    public function getInsuranceEmail()
    {
        return $this->insuranceEmail;
    }

    /**
     * Set insuranceEmail
     *
     * @param string $insuranceEmail
     * @return $this
     */
    public function setInsuranceEmail($insuranceEmail)
    {
        $this->insuranceEmail = $insuranceEmail;
        return $this;
    }

    /**
     * Get agentBrand
     *
     * @return AgentBrand
     */
    public function getAgentBrand()
    {
        return $this->agentBrand;
    }

    /**
     * Set agentBrand
     *
     * @param AgentBrand $agentBrand
     * @return $this
     */
    public function setAgentBrand(AgentBrand $agentBrand)
    {
        $this->agentBrand = $agentBrand;
        return $this;
    }

    /**
     * Get doNotContactTenant
     *
     * @return string
     */
    public function getDoNotContactTenant()
    {
        return $this->doNotContactTenant;
    }

    /**
     * Set doNotContactTenant
     *
     * @param string $doNotContactTenant
     * @return $this
     */
    public function setDoNotContactTenant($doNotContactTenant)
    {
        $this->doNotContactTenant = $doNotContactTenant;
        return $this;
    }
}


