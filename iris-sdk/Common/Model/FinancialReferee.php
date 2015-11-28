<?php

namespace Barbondev\IRISSDK\Common\Model;

use Barbondev\IRISSDK\Common\Model\Address;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class FinancialReferee
 * @todo Should implement \JsonSerializable and have private properties.  Currently doesn't for PHP <5.4 compatibility.
 *
 * @package Barbondev\IRISSDK\Common\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class FinancialReferee extends AbstractResponseModel
{
    /**
     * @var string
     */
    public $financialRefereeUuId;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $contactName;

    /**
     * @var string
     */
    public $contactPosition;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $fax;

    /**
     * @var string
     */
    public $email;

    /**
     * @var float
     */
    public $applicantAnnualIncome;

    /**
     * @var string
     */
    public $applicantPositionHeld;

    /**
     * @var string
     */
    public $payrollNumber;

    /**
     * @var string
     */
    public $employmentStartDate;

    /**
     * @var bool
     */
    public $isPermanent;

    /**
     * @var int
     */
    public $financialRefereeType;

    /**
     * @var bool
     */
    public $isValidated;

    /**
     * @var int
     */
    public $financialRefereeStatus;

    /**
     * @var bool
     */
    private $employmentChangeDuringTenancy;

    /**
     * @var bool
     */
    private $multipleJobOrPension;

    /**
     * Set address
     *
     * @param \Barbondev\IRISSDK\Common\Model\Address|null $address
     * @return $this
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return \Barbondev\IRISSDK\Common\Model\Address|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set applicantAnnualIncome
     *
     * @param float $applicantAnnualIncome
     * @return $this
     */
    public function setApplicantAnnualIncome($applicantAnnualIncome)
    {
        $this->applicantAnnualIncome = $applicantAnnualIncome;
        return $this;
    }

    /**
     * Get applicantAnnualIncome
     *
     * @return float
     */
    public function getApplicantAnnualIncome()
    {
        return $this->applicantAnnualIncome;
    }

    /**
     * Set applicantPositionHeld
     *
     * @param string $applicantPositionHeld
     * @return $this
     */
    public function setApplicantPositionHeld($applicantPositionHeld)
    {
        $this->applicantPositionHeld = $applicantPositionHeld;
        return $this;
    }

    /**
     * Get applicantPositionHeld
     *
     * @return string
     */
    public function getApplicantPositionHeld()
    {
        return $this->applicantPositionHeld;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
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
     * Set contactPosition
     *
     * @param string $contactPosition
     * @return $this
     */
    public function setContactPosition($contactPosition)
    {
        $this->contactPosition = $contactPosition;
        return $this;
    }

    /**
     * Get contactPosition
     *
     * @return string
     */
    public function getContactPosition()
    {
        return $this->contactPosition;
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
     * Set employmentStartDate
     *
     * @param string $employmentStartDate
     * @return $this
     */
    public function setEmploymentStartDate($employmentStartDate)
    {
        $this->employmentStartDate = $employmentStartDate;
        return $this;
    }

    /**
     * Get employmentStartDate
     *
     * @return string
     */
    public function getEmploymentStartDate()
    {
        return $this->employmentStartDate;
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
     * Set financialRefereeStatus
     *
     * @param int $financialRefereeStatus
     * @return $this
     */
    public function setFinancialRefereeStatus($financialRefereeStatus)
    {
        $this->financialRefereeStatus = $financialRefereeStatus;
        return $this;
    }

    /**
     * Get financialRefereeStatus
     *
     * @return int
     */
    public function getFinancialRefereeStatus()
    {
        return $this->financialRefereeStatus;
    }

    /**
     * Set financialRefereeType
     *
     * @param int $financialRefereeType
     * @return $this
     */
    public function setFinancialRefereeType($financialRefereeType)
    {
        $this->financialRefereeType = $financialRefereeType;
        return $this;
    }

    /**
     * Get financialRefereeType
     *
     * @return int
     */
    public function getFinancialRefereeType()
    {
        return $this->financialRefereeType;
    }

    /**
     * Set financialRefereeUuId
     *
     * @param string $financialRefereeUuId
     * @return $this
     */
    public function setFinancialRefereeUuId($financialRefereeUuId)
    {
        $this->financialRefereeUuId = $financialRefereeUuId;
        return $this;
    }

    /**
     * Get financialRefereeUuId
     *
     * @return string
     */
    public function getFinancialRefereeUuId()
    {
        return $this->financialRefereeUuId;
    }

    /**
     * Set isPermanent
     *
     * @param boolean $isPermanent
     * @return $this
     */
    public function setIsPermanent($isPermanent)
    {
        $this->isPermanent = $isPermanent;
        return $this;
    }

    /**
     * Get isPermanent
     *
     * @return boolean
     */
    public function getIsPermanent()
    {
        return $this->isPermanent;
    }

    /**
     * Set isValidated
     *
     * @param boolean $isValidated
     * @return $this
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;
        return $this;
    }

    /**
     * Get isValidated
     *
     * @return boolean
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Set payrollNumber
     *
     * @param string $payrollNumber
     * @return $this
     */
    public function setPayrollNumber($payrollNumber)
    {
        $this->payrollNumber = $payrollNumber;
        return $this;
    }

    /**
     * Get payrollNumber
     *
     * @return string
     */
    public function getPayrollNumber()
    {
        return $this->payrollNumber;
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
     * Set employment change during tenancy
     *
     * @param mixed $employmentChangeDuringTenancy
     * @return $this
     */
    public function setEmploymentChangeDuringTenancy($employmentChangeDuringTenancy)
    {
        $this->employmentChangeDuringTenancy = $employmentChangeDuringTenancy;
        return $this;
    }

    /**
     * Get employment change during tenancy
     *
     * @return mixed
     */
    public function getEmploymentChangeDuringTenancy()
    {
        return $this->employmentChangeDuringTenancy;
    }

    /**
     * Set multiple job or pension
     *
     * @param boolean $multipleJobOrPension
     * @return $this
     */
    public function setMultipleJobOrPension($multipleJobOrPension)
    {
        $this->multipleJobOrPension = $multipleJobOrPension;
        return $this;
    }

    /**
     * Get multiple job or pension
     *
     * @return boolean
     */
    public function getMultipleJobOrPension()
    {
        return $this->multipleJobOrPension;
    }

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
        $addressData = $data['address'];

        $address = new Address();

        // Reflect the address object ready to iterate through its properties
        $addressReflection = new \ReflectionObject($address);

        // Loop through address properties looking for matches with incoming
        //   data
        foreach ($addressReflection->getProperties() as $reflectionProperty) {

            $addressField = $reflectionProperty->getName();

            if (isset($addressData[$addressField])) {

                // String equivalent of the setter, based on the property name
                $setMethod = 'set' . ucfirst($addressField);

                // Check that the setter method exists, if so, use it
                if (method_exists($address, $setMethod)) {
                    $address->$setMethod($addressData[$addressField]);
                }

            }

        }

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'financialRefereeId' => 'financialRefereeUuId'
            ),
            array(
                'address' => $address
            )
        );
    }
}
