<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use DateTime;

/**
 * @Iris\Entity\FinancialReferee
 */
final class FinancialReferee
{
    /**
     * @var boolean
     */
    private $isVisible;

    /**
     * @Iris\Field
     * @var int
     */
    private $financialRefereeId;

    /**
     * @Iris\Field
     * @var string
     */
    private $companyName;

    /**
     * @Iris\Field
     * @var string
     */
    private $contactName;

    /**
     * @Iris\Field
     * @var string
     */
    private $contactPosition;

    /**
     * @Iris\Field
     * @var string
     */
    private $phone;

    /**
     * @Iris\Field
     * @var string
     */
    private $fax;

    /**
     * @Iris\Field
     * @var string
     */
    private $email;

    /**
     * @Iris\Field
     * @var float
     */
    private $applicantAnnualIncome;

    /**
     * @Iris\Field
     * @var string
     */
    private $applicantPositionHeld;

    /**
     * @Iris\Field
     * @var string
     */
    private $payrollNumber;

    /**
     * @Iris\Field(format = "Y-m-d")
     * @var DateTime
     */
    private $employmentStartDate;

    /**
     * @Iris\Field(accessor = "isPermanent")
     * @var boolean
     */
    private $isPermanent;

    /**
     * @Iris\Field
     * @var int
     */
    private $financialRefereeType;

    /**
     * @Iris\Field(accessor = "isValidated")
     * @var boolean
     */
    private $isValidated;

    /**
     * @Iris\Field
     * @var int
     */
    private $financialRefereeStatus;

    /**
     * @Iris\Field
     * @var Address
     */
    private $address;

    /**
     * Is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set if is visible
     *
     * @param boolean $isForeign
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        return $this;
    }
    
    /**
     * Get financial referee id
     *
     * @return int
     */
    public function getFinancialRefereeId()
    {
        return $this->financialRefereeId;
    }

    /**
     * Set financial referee id
     *
     * @param int $financialRefereeId
     * @return $this
     */
    public function setFinancialRefereeId($financialRefereeId)
    {
        $this->financialRefereeId = $financialRefereeId;
        return $this;
    }

    /**
     * Get company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set company name
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
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set contact name
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
     * Get contact position
     *
     * @return string
     */
    public function getContactPosition()
    {
        return $this->contactPosition;
    }

    /**
     * Set contact position
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
     * Get applicant annual income
     *
     * @return float
     */
    public function getApplicantAnnualIncome()
    {
        return $this->applicantAnnualIncome;
    }

    /**
     * Set applicant annual income
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
     * Get applicant annual earnings
     *
     * @return float
     */
    public function getApplicantAnnualEarnings()
    {
        return $this->applicantAnnualIncome;
    }

    /**
     * Set applicant annual earnings
     *
     * @param float $applicantAnnualEarnings
     * @return $this
     */
    public function setApplicantAnnualEarnings($applicantAnnualEarnings)
    {
        $this->applicantAnnualIncome = $applicantAnnualEarnings;
        return $this;
    }

    /**
     * Get applicant annual pension
     *
     * @return float
     */
    public function getApplicantAnnualPension()
    {
        return $this->applicantAnnualIncome;
    }

    /**
     * Set applicant annual pension
     *
     * @param float $applicantAnnualPension
     * @return $this
     */
    public function setApplicantAnnualPension($applicantAnnualPension)
    {
        $this->applicantAnnualIncome = $applicantAnnualPension;
        return $this;
    }

    /**
     * Get applicant position held
     *
     * @return string
     */
    public function getApplicantPositionHeld()
    {
        return $this->applicantPositionHeld;
    }

    /**
     * Set applicant position held
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
     * Get payroll number
     *
     * @return string
     */
    public function getPayrollNumber()
    {
        return $this->payrollNumber;
    }

    /**
     * Set payroll number
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
     * Get employment start date
     *
     * @return DateTime
     */
    public function getEmploymentStartDate()
    {
        return $this->employmentStartDate;
    }

    /**
     * Set employment start date
     *
     * @param DateTime $employmentStartDate
     * @return $this
     */
    public function setEmploymentStartDate(DateTime $employmentStartDate = null)
    {
        $this->employmentStartDate = $employmentStartDate;
        return $this;
    }

    /**
     * Get permanent status
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->isPermanent;
    }

    /**
     * Set permanent status
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
     * Get financial referee type
     *
     * @return int
     */
    public function getFinancialRefereeType()
    {
        return $this->financialRefereeType;
    }

    /**
     * Set financial referee type
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
     * Get validated status
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->isValidated;
    }

    /**
     * Set validated status
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
     * Get financial referee status
     *
     * @return int
     */
    public function getFinancialRefereeStatus()
    {
        return $this->financialRefereeStatus;
    }

    /**
     * Set financial referee status
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
}