<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\Common\Model\BankAccount;
use Barbondev\IRISSDK\Common\Model\LettingReferee;
use Barbondev\IRISSDK\Common\Model\FinancialReferee;
use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Common\Collection;
use Guzzle\Service\Command\OperationCommand;
use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;

/**
 * Class ReferencingApplication
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReferencingApplication extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $referencingApplicationUuId;

    /**
     * @var string
     */
    private $referencingCaseUuId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $middleName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $otherName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $birthDate;

    /**
     * @var int
     */
    private $employmentStatus;

    /**
     * @var int
     */
    private $residentialStatus;

    /**
     * @var float
     */
    private $grossIncome;

    /**
     * @var bool
     */
    private $hasCCJ;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $mobile;

    /**
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * @var bool
     */
    private $isRentPaidInAdvance;

    /**
     * @var array
     */
    private $financialReferees;

    /**
     * @var array
     */
    private $addressHistories;

    /**
     * @var float
     */
    private $rentShare;

    /**
     * @var int
     */
    private $completionMethod;

    /**
     * @var LettingReferee
     */
    private $lettingReferee;

    /**
     * @var int
     */
    private $signaturePreference;

    /**
     * @var int
     */
    private $applicationType;

    /**
     * @var int
     */
    private $policyLength;

    /**
     * @var bool
     */
    private $hasEmploymentChanged;

    /**
     * @var string
     */
    private $referenceNumber;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $firstCompletionAt;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    private $interimReportNodeId;

    /**
     * @var string
     */
    private $finalReportNodeId;

    /**
     * @var bool
     */
    private $canContactApplicantByPhoneAndPost;

    /**
     * @var bool
     */
    private $canContactApplicantBySMSAndEmail;

    /**
     * @var bool
     */
    private $obtainFinancialReference;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        if (self::isResponseDataIndexedArray($data)) {

            $applications = new Collection();

            foreach ($data as $key => $applicationData) {
                $applications->add($key, self::hydrate($applicationData));
            }

            return $applications;
        }
        else {
            return self::hydrate($data);
        }
    }

    /**
     * Hydrate a single application
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        $bankAccount = isset($data['bankAccount']) ? self::hydrateModelProperties(
            new BankAccount(),
            $data['bankAccount']
        ) : null;

        $lettingReferee = null;
        if (isset($data['lettingReferee'])) {
            $lettingRefereeAddress = isset($data['lettingReferee']['address']) ? self::hydrateModelProperties(
                new Address(),
                $data['lettingReferee']['address']
            ) : null;

            $lettingReferee = self::hydrateModelProperties(
                new LettingReferee(),
                $data['lettingReferee'],
                array(),
                array(
                    'address' => $lettingRefereeAddress
                )
            );
        }

        $financialReferees = array();
        if (isset($data['financialReferees']) && is_array($data['financialReferees'])) {
            foreach ($data['financialReferees'] as $key => $object) {

                $financialRefereeAddress = isset($object['address']) ? self::hydrateModelProperties(
                    new Address(),
                    $object['address']
                ) : null;

                $financialReferees[$key] = self::hydrateModelProperties(
                    new FinancialReferee(),
                    $object,
                    array(
                        'financialRefereeId' => 'financialRefereeUuId',
                    ),
                    array(
                        'address' => $financialRefereeAddress,
                    )
                );
            }
        }

        $addressHistories = array();
        if (isset($data['addressHistories']) && is_array($data['addressHistories'])) {
            foreach ($data['addressHistories'] as $key => $object) {

                $addressHistoryAddress = isset($object['address']) ? self::hydrateModelProperties(
                    new Address(),
                    $object['address']
                ) : null;

                $addressHistories[$key] = self::hydrateModelProperties(
                    new AddressHistory(),
                    $object,
                    array(
                        'addressHistoryId' => 'addressHistoryUuId',
                    ),
                    array(
                        'address' => $addressHistoryAddress,
                    )
                );
            }
        }

        $product = isset($data['product']) ? self::hydrateModelProperties(
            new Product(),
            $data['product'],
            array(
                'productId' => 'id',
            )
        ) : null;

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'applicationId' => 'referencingApplicationUuId',
                'caseId' => 'referencingCaseUuId',
                'canEmploymentChange' => 'hasEmploymentChanged',
            ),
            array(
                'bankAccount' => $bankAccount,
                'lettingReferee' => $lettingReferee,
                'addressHistories' => $addressHistories,
                'financialReferees' => $financialReferees,
                'product' => $product,
                'productId' => $product instanceof Product ? $product->getId() : null,
            )
        );
    }

    /**
     * Set addressHistories
     *
     * @param array $addressHistories
     * @return $this
     */
    public function setAddressHistories(array $addressHistories)
    {
        $this->addressHistories = $addressHistories;
        return $this;
    }

    /**
     * Get addressHistories
     *
     * @return \Guzzle\Common\Collection
     */
    public function getAddressHistories()
    {
        return $this->addressHistories;
    }

    /**
     * Set applicationType
     *
     * @param int $applicationType
     * @return $this
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
        return $this;
    }

    /**
     * Get applicationType
     *
     * @return int
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * Set bankAccount
     *
     * @param \Barbondev\IRISSDK\Common\Model\BankAccount $bankAccount
     * @return $this
     */
    public function setBankAccount(BankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    /**
     * Get bankAccount
     *
     * @return \Barbondev\IRISSDK\Common\Model\BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Set birthDate
     *
     * @param string $birthDate
     * @return $this
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return string
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set completionMethod
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
     * Get completionMethod
     *
     * @return int
     */
    public function getCompletionMethod()
    {
        return $this->completionMethod;
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
     * Set employmentStatus
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
     * Get employmentStatus
     *
     * @return int
     */
    public function getEmploymentStatus()
    {
        return $this->employmentStatus;
    }

    /**
     * Set financialReferees
     *
     * @param array $financialReferees
     * @return $this
     */
    public function setFinancialReferees(array $financialReferees)
    {
        $this->financialReferees = $financialReferees;
        return $this;
    }

    /**
     * Get financialReferees
     *
     * @return \Guzzle\Common\Collection
     */
    public function getFinancialReferees()
    {
        return $this->financialReferees;
    }

    /**
     * Set firstName
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
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set grossIncome
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
     * Get grossIncome
     *
     * @return float
     */
    public function getGrossIncome()
    {
        return $this->grossIncome;
    }

    /**
     * Set hasCCJ
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
     * Get hasCCJ
     *
     * @return boolean
     */
    public function getHasCCJ()
    {
        return $this->hasCCJ;
    }

    /**
     * Set hasEmploymentChanged
     *
     * @param boolean $hasEmploymentChanged
     * @return $this
     */
    public function setHasEmploymentChanged($hasEmploymentChanged)
    {
        $this->hasEmploymentChanged = $hasEmploymentChanged;
        return $this;
    }

    /**
     * Get hasEmploymentChanged
     *
     * @return boolean
     */
    public function getHasEmploymentChanged()
    {
        return $this->hasEmploymentChanged;
    }

    /**
     * Set isRentPaidInAdvance
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
     * Get isRentPaidInAdvance
     *
     * @return boolean
     */
    public function getIsRentPaidInAdvance()
    {
        return $this->isRentPaidInAdvance;
    }

    /**
     * Set lastName
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
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lettingReferee
     *
     * @param \Barbondev\IRISSDK\Common\Model\LettingReferee $lettingReferee
     * @return $this
     */
    public function setLettingReferee($lettingReferee)
    {
        $this->lettingReferee = $lettingReferee;
        return $this;
    }

    /**
     * Get lettingReferee
     *
     * @return \Barbondev\IRISSDK\Common\Model\LettingReferee
     */
    public function getLettingReferee()
    {
        return $this->lettingReferee;
    }

    /**
     * Set middleName
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
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
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
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set otherName
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
     * Get otherName
     *
     * @return string
     */
    public function getOtherName()
    {
        return $this->otherName;
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
     * Set policyLength
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
     * Get policyLength
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Set productId
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
     * Get productId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set referencingApplicationUuId
     *
     * @param string $referencingApplicationUuId
     * @return $this
     */
    public function setReferencingApplicationUuId($referencingApplicationUuId)
    {
        $this->referencingApplicationUuId = $referencingApplicationUuId;
        return $this;
    }

    /**
     * Get referencingApplicationUuId
     *
     * @return string
     */
    public function getReferencingApplicationUuId()
    {
        return $this->referencingApplicationUuId;
    }

    /**
     * Set referencingCaseUuId
     *
     * @param string $referencingCaseUuId
     * @return $this
     */
    public function setReferencingCaseUuId($referencingCaseUuId)
    {
        $this->referencingCaseUuId = $referencingCaseUuId;
        return $this;
    }

    /**
     * Get referencingCaseUuId
     *
     * @return string
     */
    public function getReferencingCaseUuId()
    {
        return $this->referencingCaseUuId;
    }

    /**
     * Set rentShare
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
     * Get rentShare
     *
     * @return float
     */
    public function getRentShare()
    {
        return $this->rentShare;
    }

    /**
     * Set residentialStatus
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
     * Get residentialStatus
     *
     * @return int
     */
    public function getResidentialStatus()
    {
        return $this->residentialStatus;
    }

    /**
     * Set signaturePreference
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
     * Get signaturePreference
     *
     * @return int
     */
    public function getSignaturePreference()
    {
        return $this->signaturePreference;
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set referenceNumber
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * Get referenceNumber
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
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
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set status
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
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set firstCompletionAt
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
     * Get firstCompletionAt
     *
     * @return string
     */
    public function getFirstCompletionAt()
    {
        return $this->firstCompletionAt;
    }

    /**
     * Set product
     *
     * @param \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set finalReportNodeId
     *
     * @param string $finalReportNodeId
     * @return $this
     */
    public function setFinalReportNodeId($finalReportNodeId)
    {
        $this->finalReportNodeId = $finalReportNodeId;
        return $this;
    }

    /**
     * Get finalReportNodeId
     *
     * @return string
     */
    public function getFinalReportNodeId()
    {
        return $this->finalReportNodeId;
    }

    /**
     * Set interimReportNodeId
     *
     * @param string $interimReportNodeId
     * @return $this
     */
    public function setInterimReportNodeId($interimReportNodeId)
    {
        $this->interimReportNodeId = $interimReportNodeId;
        return $this;
    }

    /**
     * Get interimReportNodeId
     *
     * @return string
     */
    public function getInterimReportNodeId()
    {
        return $this->interimReportNodeId;
    }

    /**
     * Set canContactApplicantByPhoneAndPost
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
     * Get canContactApplicantByPhoneAndPost
     *
     * @return boolean
     */
    public function getCanContactApplicantByPhoneAndPost()
    {
        return $this->canContactApplicantByPhoneAndPost;
    }

    /**
     * Set canContactApplicantBySMSAndEmail
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
     * Get canContactApplicantBySMSAndEmail
     *
     * @return boolean
     */
    public function getCanContactApplicantBySMSAndEmail()
    {
        return $this->canContactApplicantBySMSAndEmail;
    }

    /**
     * Get obtainFinancialReference
     *
     * @return boolean
     */
    public function getObtainFinancialReference()
    {
        return $this->obtainFinancialReference;
    }

    /**
     * Set obtainFinancialReference
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
     * Get the applicant's full name including title and middle name.
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = '';

        if ($this->getTitle() !== '') {
            $fullName .= sprintf('%s ', $this->getTitle());
        }

        if ($this->getFirstName() !== '') {
            $fullName .= sprintf('%s ', $this->getFirstName());
        }

        if ($this->getMiddleName() !== '') {
            $fullName .= sprintf('%s ', $this->getMiddleName());
        }

        if ($this->getLastName() !== '') {
            $fullName .= sprintf('%s ', $this->getLastName());
        }

        return trim($fullName);
    }
}