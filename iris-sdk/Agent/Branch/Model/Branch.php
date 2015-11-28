<?php

namespace Barbondev\IRISSDK\Agent\Branch\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;
use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\Agent\Brand\Model\Brand;

/**
 * Class Branch
 *
 * @package Barbondev\IRISSDK\Agent\Branch\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Branch extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $agentBranchUuId;

    /**
     * @var string
     */
    private $agentSchemeNumber;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var Address
     */
    private $correspondenceAddress;

    /**
     * @var string
     */
    private $contactName;

    /**
     * @var string
     */
    private $email;

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
    private $status;

    /**
     * @var string
     */
    private $websiteUrl;

    /**
     * @var \DateTime|null
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $hasSMS;

    /**
     * @var bool
     */
    private $hasMailer;

    /**
     * @var bool
     */
    private $isPremier;

    /**
     * @var bool
     */
    private $hasTenantMailerOptin;

    /**
     * @var bool
     */
    private $hasInterimReport;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $generalEmail;

    /**
     * @var string
     */
    private $referencingEmail;

    /**
     * @var string
     */
    private $invoiceEmail;

    /**
     * @var string
     */
    private $insuranceEmail;

    /**
     * @var string
     */
    private $rentGuaranteeRenewalsEmail;

    /**
     * @var Brand
     */
    private $agentBrand;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        // Indexed array of branches
        if (self::isResponseDataIndexedArray($data)) {

            $branches = new Collection();

            foreach ($data as $key => $branchData) {

                $branches->add(
                    $key,
                    self::hydrateModelProperties(
                        new self(),
                        $branchData,
                        array(
                            'agentBranchId' => 'agentBranchUuId',
                        )
                    )
                );
            }

            return $branches;
        }

        // Single branch
        else {

            $address = self::hydrateModelProperties(
                new Address(),
                $data['address']
            );

            $correspondenceAddress = self::hydrateModelProperties(
                new Address(),
                $data['correspondenceAddress']
            );

            $brandAddress = self::hydrateModelProperties(
                new Address(),
                $data['agentBrand']['address']
            );

            $brand = self::hydrateModelProperties(
                new Brand(),
                $data['agentBrand'],
                array(),
                array(
                    'address' => $brandAddress,
                )
            );

            return self::hydrateModelProperties(
                new self(),
                $data,
                array(),
                array(
                    'address' => $address,
                    'correspondenceAddress' => $correspondenceAddress,
                    'agentBrand' => $brand,
                )
            );
        }
    }

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
     * Set agentBranchUuId
     *
     * @param string $agentBranchUuId
     * @return $this
     */
    public function setAgentBranchUuId($agentBranchUuId)
    {
        $this->agentBranchUuId = $agentBranchUuId;
        return $this;
    }

    /**
     * Get agentBranchUuId
     *
     * @return string
     */
    public function getAgentBranchUuId()
    {
        return $this->agentBranchUuId;
    }

    /**
     * Set agentBrand
     *
     * @param \Barbondev\IRISSDK\Agent\Brand\Model\Brand $agentBrand
     * @return $this
     */
    public function setAgentBrand(Brand $agentBrand)
    {
        $this->agentBrand = $agentBrand;
        return $this;
    }

    /**
     * Get agentBrand
     *
     * @return \Barbondev\IRISSDK\Agent\Brand\Model\Brand
     */
    public function getAgentBrand()
    {
        return $this->agentBrand;
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
     * Get agentSchemeNumber
     *
     * @return string
     */
    public function getAgentSchemeNumber()
    {
        return $this->agentSchemeNumber;
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
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set correspondenceAddress
     *
     * @param \Barbondev\IRISSDK\Common\Model\Address $correspondenceAddress
     * @return $this
     */
    public function setCorrespondenceAddress(Address $correspondenceAddress)
    {
        $this->correspondenceAddress = $correspondenceAddress;
        return $this;
    }

    /**
     * Get correspondenceAddress
     *
     * @return \Barbondev\IRISSDK\Common\Model\Address
     */
    public function getCorrespondenceAddress()
    {
        return $this->correspondenceAddress;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
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
     * Get generalEmail
     *
     * @return string
     */
    public function getGeneralEmail()
    {
        return $this->generalEmail;
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
     * Get hasInterimReport
     *
     * @return boolean
     */
    public function getHasInterimReport()
    {
        return $this->hasInterimReport;
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
     * Get hasMailer
     *
     * @return boolean
     */
    public function getHasMailer()
    {
        return $this->hasMailer;
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
     * Get hasSMS
     *
     * @return boolean
     */
    public function getHasSMS()
    {
        return $this->hasSMS;
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
     * Get hasTenantMailerOptin
     *
     * @return boolean
     */
    public function getHasTenantMailerOptin()
    {
        return $this->hasTenantMailerOptin;
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
     * Get insuranceEmail
     *
     * @return string
     */
    public function getInsuranceEmail()
    {
        return $this->insuranceEmail;
    }

    /**
     * Set invoiceEmail
     *
     * @param string $invoiceEmail
     * @return $this
     */
    public function setInvoiceEmail($invoiceEmail)
    {
        $this->invoiceEmail = $invoiceEmail;
        return $this;
    }

    /**
     * Get invoiceEmail
     *
     * @return string
     */
    public function getInvoiceEmail()
    {
        return $this->invoiceEmail;
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
     * Get isPremier
     *
     * @return boolean
     */
    public function getIsPremier()
    {
        return $this->isPremier;
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
     * Get referencingEmail
     *
     * @return string
     */
    public function getReferencingEmail()
    {
        return $this->referencingEmail;
    }

    /**
     * Set rentGuaranteeRenewalsEmail
     *
     * @param string $rentGuaranteeRenewalsEmail
     * @return $this
     */
    public function setRentGuaranteeRenewalsEmail($rentGuaranteeRenewalsEmail)
    {
        $this->rentGuaranteeRenewalsEmail = $rentGuaranteeRenewalsEmail;
        return $this;
    }

    /**
     * Get rentGuaranteeRenewalsEmail
     *
     * @return string
     */
    public function getRentGuaranteeRenewalsEmail()
    {
        return $this->rentGuaranteeRenewalsEmail;
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
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * Get websiteUrl
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }
}