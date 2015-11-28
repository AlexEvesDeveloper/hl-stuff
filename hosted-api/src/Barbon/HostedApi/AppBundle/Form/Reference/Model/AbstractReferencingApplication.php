<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\BankAccount;
use Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee;
use Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress;
use Barbon\HostedApi\AppBundle\Form\Common\Model\Product;
use Barbon\IrisRestClient\Annotation as Iris;
use DateTime;

abstract class AbstractReferencingApplication
{
    /**
     * @var boolean
     */
    private $isVisible;

    /**
     * @Iris\Id
     * @Iris\Field
     * @var int
     */
    private $applicationId;

    /**
     * @Iris\Field
     * @var string
     */
    private $referenceNumber;

    /**
     * @Iris\Field
     * @var Product
     */
    private $product;

    /**
     * @Iris\Field
     * @var int
     *
     * this is left for BC, but is now accessable through the $product object: $this->getProduct()->getProductId()
     */
    private $productId;

    /**
     * @Iris\Field
     * @var string
     */
    private $title;

    /**
     * @Iris\Field
     * @var string
     */
    private $firstName;

    /**
     * @Iris\Field
     * @var string
     */
    private $middleName;

    /**
     * @Iris\Field
     * @var string
     */
    private $lastName;

    /**
     * @Iris\Field
     * @var string
     */
    private $otherName;

    /**
     * @Iris\Field
     * @var string
     */
    private $email;

    /**
     * @Iris\Field(format = "Y-m-d")
     * @var DateTime
     */
    private $birthDate;

    /**
     * @Iris\Field
     * @var int
     */
    private $employmentStatus;

    /**
     * @Iris\Field
     * @var int
     */
    private $residentialStatus;

    /**
     * @Iris\Field
     * @var float
     */
    private $grossIncome;

    /**
     * @Iris\Field(accessor = "hasCCJ")
     * @var boolean
     */
    private $hasCCJ;

    /**
     * @Iris\Field
     * @var string
     */
    private $phone;

    /**
     * @Iris\Field
     * @var string
     */
    private $mobile;

    /**
     * @Iris\Field(accessor = "isRentPaidInAdvance")
     * @var boolean
     */
    private $isRentPaidInAdvance;

    /**
     * @Iris\Field
     * @var float
     */
    private $rentShare;

    /**
     * @Iris\Field
     * @var int
     */
    private $completionMethod;

    /**
     * @Iris\Field
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * @Iris\Field
     * @var array
     */
    private $financialReferees;

    /**
     * @Iris\Field
     * @var array
     */
    private $addressHistories;

    /**
     * @Iris\Field
     * @var int
     */
    private $signaturePreference;

    /**
     * @Iris\Field
     * @var int
     */
    private $policyLength;

    /**
     * @Iris\Field(accessor = "canEmploymentChange")
     * @var boolean
     */
    private $canEmploymentChange;

    /**
     * @Iris\Field
     * @var string
     */
    private $caseId;

    /**
     * @Iris\Field
     * @var int
     */
    private $channel;

    /**
     * @Iris\Field
     * @var string
     */
    private $firstCompletionAt;

    /**
     * @Iris\Field(accessor = "canContactApplicantByPhoneAndPost")
     * @var boolean
     */
    private $canContactApplicantByPhoneAndPost;

    /**
     * @Iris\Field(accessor = "canContactApplicantBySMSAndEmail")
     * @var boolean
     */
    private $canContactApplicantBySMSAndEmail;

    /**
     * @Iris\Field
     * @var boolean
     */
    private $obtainFinancialReference;

    /**
     * @Iris\Field
     * @var int
     */
    private $status;

    /**
     * @Iris\Field
     * @var int
     */
    private $applicationType;


    /*
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
     * @param $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * Get application id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set application id
     *
     * @param int $applicationId
     * @return $this
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
        return $this;
    }

    /**
     * Get the application reference number
     *
     * @return mixed
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set the application reference number
     *
     * @param mixed $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set product id
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get middle name
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set middle name
     *
     * @param string $middleName
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get other name
     *
     * @return string
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     * Set other name
     *
     * @param string $otherName
     * @return $this
     */
    public function setOtherName($otherName)
    {
        $this->otherName = $otherName;
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
     * Get birth date
     *
     * @return DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birth date
     *
     * @param DateTime $birthDate
     * @return $this
     */
    public function setBirthDate(DateTime $birthDate = null)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * Get employment status
     *
     * @return int
     */
    public function getEmploymentStatus()
    {
        return $this->employmentStatus;
    }

    /**
     * Set employment status
     *
     * @param int $employmentStatus
     * @return $this
     */
    public function setEmploymentStatus($employmentStatus)
    {
        $this->employmentStatus = $employmentStatus;
        return $this;
    }

    /**
     * Get residential status
     *
     * @return int
     */
    public function getResidentialStatus()
    {
        return $this->residentialStatus;
    }

    /**
     * Set residential status
     *
     * @param int $residentialStatus
     * @return $this
     */
    public function setResidentialStatus($residentialStatus)
    {
        $this->residentialStatus = $residentialStatus;
        return $this;
    }

    /**
     * Get gross income
     *
     * @return float
     */
    public function getGrossIncome()
    {
        return $this->grossIncome;
    }

    /**
     * Set gross income
     *
     * @param float $grossIncome
     * @return $this
     */
    public function setGrossIncome($grossIncome)
    {
        $this->grossIncome = $grossIncome;
        return $this;
    }

    /**
     * Has county court judgements
     *
     * @return boolean
     */
    public function hasCCJ()
    {
        return $this->hasCCJ;
    }

    /**
     * Set if has county court judgements
     *
     * @param boolean $hasCCJ
     * @return $this
     */
    public function setHasCCJ($hasCCJ)
    {
        $this->hasCCJ = $hasCCJ;
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
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Is rent paid in advance
     *
     * @return boolean
     */
    public function isRentPaidInAdvance()
    {
        return $this->isRentPaidInAdvance;
    }

    /**
     * Set if rent is paid in advance
     *
     * @param boolean $isRentPaidInAdvance
     * @return $this
     */
    public function setIsRentPaidInAdvance($isRentPaidInAdvance)
    {
        $this->isRentPaidInAdvance = $isRentPaidInAdvance;
        return $this;
    }

    /**
     * Get rent share
     *
     * @return float
     */
    public function getRentShare()
    {
        return $this->rentShare;
    }

    /**
     * Set rent share
     *
     * @param float $rentShare
     * @return $this
     */
    public function setRentShare($rentShare)
    {
        $this->rentShare = $rentShare;
        return $this;
    }

    /**
     * Get completion method
     *
     * @return int
     */
    public function getCompletionMethod()
    {
        return $this->completionMethod;
    }

    /**
     * Set completion method
     *
     * @param int $completionMethod
     * @return $this
     */
    public function setCompletionMethod($completionMethod)
    {
        $this->completionMethod = $completionMethod;
        return $this;
    }

    /**
     * Get bank account
     *
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Set bank account
     *
     * @param BankAccount $bankAccount
     * @return $this
     */
    public function setBankAccount(BankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    /**
     * Get financial referees
     *
     * @return FinancialReferee[]
     */
    public function getFinancialReferees()
    {
        return $this->financialReferees;
    }

    /**
     * Set financial referees
     *
     * @param FinancialReferee[] $financialReferees
     * @return $this
     */
    public function setFinancialReferees(array $financialReferees)
    {
        $this->financialReferees = $financialReferees;
        return $this;
    }

    /**
     * Get address history
     *
     * @return PreviousAddress[]
     */
    public function getAddressHistories()
    {
        return $this->addressHistories;
    }

    /**
     * Set address history
     *
     * @param PreviousAddress[] $addressHistories
     * @return $this
     */
    public function setAddressHistories(array $addressHistories)
    {
        $this->addressHistories = $addressHistories;
        return $this;
    }

    /**
     * Get signature preference
     *
     * @return int
     */
    public function getSignaturePreference()
    {
        return $this->signaturePreference;
    }

    /**
     * Set signature preference
     *
     * @param int $signaturePreference
     * @return $this
     */
    public function setSignaturePreference($signaturePreference)
    {
        $this->signaturePreference = $signaturePreference;
        return $this;
    }

    /**
     * Get policy length
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Set policy length
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
     * Can employment change
     *
     * @return boolean
     */
    public function canEmploymentChange()
    {
        return $this->canEmploymentChange;
    }

    /**
     * Set if employment can change
     *
     * @param boolean $canEmploymentChange
     * @return $this
     */
    public function setCanEmploymentChange($canEmploymentChange)
    {
        $this->canEmploymentChange = $canEmploymentChange;
        return $this;
    }

    /**
     * Get case id
     *
     * @return string
     */
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * Set case id
     *
     * @param string $caseId
     * @return $this
     */
    public function setCaseId($caseId)
    {
        $this->caseId = $caseId;
        return $this;
    }

    /**
     * Get channel
     *
     * @return int
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set channel
     *
     * @param int $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * Get first completed at
     *
     * @return string
     */
    public function getFirstCompletionAt()
    {
        return $this->firstCompletionAt;
    }

    /**
     * Set first completed at
     *
     * @param string $firstCompletionAt
     * @return $this
     */
    public function setFirstCompletionAt($firstCompletionAt)
    {
        $this->firstCompletionAt = $firstCompletionAt;
        return $this;
    }

    /**
     * Can contact applicant by phone or post
     *
     * @return boolean
     */
    public function canContactApplicantByPhoneAndPost()
    {
        return $this->canContactApplicantByPhoneAndPost;
    }

    /**
     * Set if can contact applicant by phone or post
     *
     * @param boolean $canContactApplicantByPhoneAndPost
     * @return $this
     */
    public function setCanContactApplicantByPhoneAndPost($canContactApplicantByPhoneAndPost)
    {
        $this->canContactApplicantByPhoneAndPost = $canContactApplicantByPhoneAndPost;
        return $this;
    }

    /**
     * Can contact applicant by sms or email
     *
     * @return boolean
     */
    public function canContactApplicantBySMSAndEmail()
    {
        return $this->canContactApplicantBySMSAndEmail;
    }

    /**
     * Set if can contact applicant by sms or email
     *
     * @param boolean $canContactApplicantBySMSAndEmail
     * @return $this
     */
    public function setCanContactApplicantBySMSAndEmail($canContactApplicantBySMSAndEmail)
    {
        $this->canContactApplicantBySMSAndEmail = $canContactApplicantBySMSAndEmail;
        return $this;
    }

    /**
     * Get if obtaining financial reference
     *
     * @return boolean
     */
    public function getObtainFinancialReference()
    {
        return $this->obtainFinancialReference;
    }

    /**
     * Set if obtaining financial reference
     *
     * @param boolean $obtainFinancialReference
     * @return $this
     */
    public function setObtainFinancialReference($obtainFinancialReference)
    {
        $this->obtainFinancialReference = $obtainFinancialReference;
        return $this;
    }

    /**
     * Get the application status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the application status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the application type
     *
     * @return int
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * Set the application type
     *
     * @param int $applicationType
     * @return $this
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
        return $this;
    }
}
