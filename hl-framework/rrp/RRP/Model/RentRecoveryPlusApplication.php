<?php

namespace RRP\Model;

use RRP\Common\ReferenceTypes as ReferenceType;
use RRP\Common\Titles;

/**
 * Class RentRecoveryPlusApplication
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusApplication extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var int
     */
    private $policyLength;

    /**
     * @var ReferenceType
     */
    protected $referenceType;

    /**
     * @var String
     */
    protected $referenceNumber;

    /**
     * @var string
     */
    private $otherProvider;

    /**
     * @var bool
     */
    private $isNilExcess;

    /**
     * @var bool
     */
    private $isContinuationOfExistingPolicy;

    /**
     * @var string
     */
    private $existingPolicyRef;

    /**
     * @var bool
     */
    private $isExistingPolicyToBeCancelled;

    /**
     * @var string
     */
    private $cancellationObjections;

    /**
     * @var
     */
    private $propertyLetType;

    /**
     * @var bool
     */
    private $hasLandlordPermission;

    /**
     * @var string
     */
    private $propertyAddress1;

    /**
     * @var string
     */
    private $propertyAddress2;

    /**
     * @var string
     */
    private $propertyAddress3;

    /**
     * @var string
     */
    private $propertyPostcode;

    /**
     * @var float
     */
    private $propertyRental;

    /**
     * @var float
     */
    private $propertyDeposit;

    /**
     * @var bool
     */
    private $hasNilDepositInsurance;

    /**
     * @var bool
     */
    private $hasPaidRentInAdvance;

    /**
     * @var \DateTime
     */
    private $tenancyStartAt;

    /**
     * @var \DateTime
     */
    private $policyStartAt;

    /**
     * @var string
     */
    private $landlordTitle;

    /**
     * @var string
     */
    private $landlordOtherTitle;

    /**
     * @var string
     */
    private $landlordFirstName;

    /**
     * @var string
     */
    private $landlordLastName;

    /**
     * @var string
     */
    private $landlordEmailAddress;

    /**
     * @var string
     */
    private $landlordPhoneNumber;

    /**
     * @var string
     */
    private $landlordAddress1;

    /**
     * @var string
     */
    private $landlordAddress2;

    /**
     * @var string
     */
    private $landlordAddress3;

    /**
     * @var string
     */
    private $landlordPostcode;

    /**
     * @var string
     */
    private $landlordCountry;

    /**
     * @var bool
     */
    private $isPayMonthly;

    /**
    * @var bool
    */
    protected $hasPossibleClaimCircumstances;
    
    /**
    * @var string
    */
    protected $claimInfo;
    
    /**
    * @var bool
    */
    protected $hasPermittedOccupiersOnly;
    
    /**
    * @var bool
    */
    protected $hasTenancyDisputes;

    /**
     * @var bool
     */
    protected $hasPriorClaims;

    /**
     * @var bool
     */
    protected $isDepositSufficient;

    /**
     * @var string
     */
    private $tenancyAgreementType;

    /**
     * @var bool
     */
    private $isXhr;

    /**
     * @var string
     */
    private $updateList;

    /**
     * @var array
     */
    private $references;

    /**
     * RentRecoveryPlusApplication constructor.
     */
    public function __construct()
    {
        $this->references = array();
    }

    /**
     * Hydrate a single application
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getMappedProperties(),
            array(
                'isExistingPolicyToBeCancelled' => 1,
                'hasPaidRentInAdvance' => 0
            )
        );
    }

    /**
     * As above but these fields have different names in the two classes
     *
     * @return array
     */
    private static function getMappedProperties()
    {
        return array(
            'startDate' => 'PolicyStartAt',
            'endDate' => 'PolicyEndAt',
        );
    }

    /**
     * Returns the lower case product name
     *
     * @return string
     */
    public function getProductName()
    {
        return 'rentrecoveryp';
    }

    /**
     * Converts the date to database format
     *
     * @param string $inputDate
     * @return \DateTime
     */
    private function transformDate($inputDate)
    {
        if ($inputDate instanceof \DateTime) {
            return $inputDate;
        } else if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return null;
        }
        return \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime(str_replace('/', '-', $inputDate))));
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Gets the 'IsContinuationOfExistingPolicy' flag
     *
     * @return boolean
     */
    public function getIsContinuationOfExistingPolicy()
    {
        if ($this->isContinuationOfExistingPolicy) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'IsContinuationOfExistingPolicy' flag
     *
     * @param boolean $isContinuationOfExistingPolicy
     * @return $this
     */
    public function setIsContinuationOfExistingPolicy($isContinuationOfExistingPolicy)
    {
        $this->isContinuationOfExistingPolicy = $isContinuationOfExistingPolicy;
        return $this;
    }

    /**
     * Gets the existing policy ref
     *
     * @return string
     */
    public function getExistingPolicyRef()
    {
        return $this->existingPolicyRef;
    }

    /**
     * Sets the existing policy ref
     *
     * @param string $existingPolicyRef
     * @return $this
     */
    public function setExistingPolicyRef($existingPolicyRef)
    {
        $this->existingPolicyRef = $existingPolicyRef;
        return $this;
    }

    /**
     * Gets the 'isExistingPolicyToBeCancelled' flag
     *
     * @return boolean
     */
    public function getIsExistingPolicyToBeCancelled()
    {
        if ($this->isExistingPolicyToBeCancelled) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'isExistingPolicyToBeCancelled' flag
     *
     * @param boolean $isExistingPolicyToBeCancelled
     * @return $this
     */
    public function setIsExistingPolicyToBeCancelled($isExistingPolicyToBeCancelled)
    {
        $this->isExistingPolicyToBeCancelled = $isExistingPolicyToBeCancelled;
        return $this;
    }

    /**
     * Gets the cancellation objections
     *
     * @return string
     */
    public function getCancellationObjections()
    {
        return $this->cancellationObjections;
    }

    /**
     * Sets the cancellation objections
     *
     * @param string $cancellationObjections
     * @return $this
     */
    public function setCancellationObjections($cancellationObjections)
    {
        $this->cancellationObjections = $cancellationObjections;
        return $this;
    }

    /**
     * Gets the excess string
     *
     * @return string
     */
    public function getExcess()
    {
        if ($this->isNilExcess) {
            return 'Nil Excess';
        }
        return '1 Month Excess';
    }

    /**
     * Gets the 'isNilExcess' flag
     *
     * @return boolean
     */
    public function getIsNilExcess()
    {
        if ($this->isNilExcess) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'isNilExcess' flag
     *
     * @param boolean $isNilExcess
     * @return $this
     */
    public function setIsNilExcess($isNilExcess)
    {
        $this->isNilExcess = $isNilExcess;
        return $this;
    }

    /**
     * Gets the policy length
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Sets the policy length
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
     * Gets the other provider
     *
     * @return string
     */
    public function getOtherProvider()
    {
        return $this->otherProvider;
    }

    /**
     * Sets the other provider
     *
     * @param string $otherProvider
     * @return $this
     */
    public function setOtherProvider($otherProvider)
    {
        $this->otherProvider = $otherProvider;
        return $this;
    }

    /**
     * Gets the reference number
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Sets the reference number
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
     * Gets the reference type
     *
     * @return string
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }

    /**
     * Sets the reference type
     *
     * @param string $referenceType
     * @return $this
     */
    public function setReferenceType($referenceType)
    {
        $this->referenceType = $referenceType;
        return $this;
    }

    /**
     * Gets the landlord email address
     *
     * @return string
     */
    public function getLandlordEmailAddress()
    {
        return $this->landlordEmailAddress;
    }

    /**
     * Sets the landlord email address
     *
     * @param string $landlordEmailAddress
     * @return $this
     */
    public function setLandlordEmailAddress($landlordEmailAddress)
    {
        $this->landlordEmailAddress = $landlordEmailAddress;
        return $this;
    }

    /**
     * Gets the landlord full name including title
     *
     * @return string
     */
    public function getLandlordFullName()
    {
        return sprintf('%s %s %s', $this->landlordTitle, $this->landlordFirstName, $this->landlordLastName);
    }

    /**
     * Gets the landlord first name
     *
     * @return string
     */
    public function getLandlordFirstName()
    {
        return $this->landlordFirstName;
    }

    /**
     * Sets the landlord first name
     *
     * @param string $landlordFirstName
     * @return $this
     */
    public function setLandlordFirstName($landlordFirstName)
    {
        $this->landlordFirstName = $landlordFirstName;
        return $this;
    }

    /**
     * Gets the landlord last name
     *
     * @return string
     */
    public function getLandlordLastName()
    {
        return $this->landlordLastName;
    }

    /**
     * Sets the landlord last name
     *
     * @param string $landlordLastName
     * @return $this
     */
    public function setLandlordLastName($landlordLastName)
    {
        $this->landlordLastName = $landlordLastName;
        return $this;
    }

    /**
     * Gets the landlord phone number
     *
     * @return string
     */
    public function getLandlordPhoneNumber()
    {
        return $this->landlordPhoneNumber;
    }

    /**
     * Sets the landlord phone number
     *
     * @param string $landlordPhoneNumber
     * @return $this
     */
    public function setLandlordPhoneNumber($landlordPhoneNumber)
    {
        $this->landlordPhoneNumber = $landlordPhoneNumber;
        return $this;
    }

    /**
     * Sets the landlord title
     *
     * @return string
     */
    public function getLandlordTitle()
    {
        return $this->landlordTitle;
    }

    /**
     * Gets the landlord title
     *
     * @param string $landlordTitle
     * @return $this
     */
    public function setLandlordTitle($landlordTitle)
    {
        if (Titles::isOtherRequired($landlordTitle)) {
            $this->landlordTitle = Titles::TITLE_OTHER;
            $this->landlordOtherTitle = $landlordTitle;
        }
        else {
            $this->landlordTitle = $landlordTitle;

        }
        return $this;
    }

    /**
     * Sets the landlord other title
     *
     * @return string
     */
    public function getLandlordOtherTitle()
    {
        return $this->landlordOtherTitle;
    }

    /**
     * Gets the landlord other title
     *
     * @param string $landlordOtherTitle
     * @return $this
     */
    public function setLandlordOtherTitle($landlordOtherTitle)
    {
        $this->landlordOtherTitle = $landlordOtherTitle;
        return $this;
    }

    /**
     * Gets the landlord's full address excluding postcode
     *
     * @return string
     */
    public function getLandlordAddress()
    {
        $address = $this->landlordAddress1;
        if ($this->landlordAddress2) {
            $address .= ', ' . $this->landlordAddress2;
        }
        if ($this->landlordAddress3) {
            $address .= ', ' . $this->landlordAddress3;
        }
        return $address;
    }

    /**
     * Gets the landlord address line 1
     *
     * @return string
     */
    public function getLandlordAddress1()
    {
        return $this->landlordAddress1;
    }

    /**
     * Sets the landlord address line 1
     *
     * @param string $landlordAddress1
     * @return $this
     */
    public function setLandlordAddress1($landlordAddress1)
    {
        $this->landlordAddress1 = $landlordAddress1;
        return $this;
    }

    /**
     * Gets the landlord address line 2
     *
     * @return string
     */
    public function getLandlordAddress2()
    {
        return $this->landlordAddress2;
    }

    /**
     * Sets the landlord address line 2
     *
     * @param string $landlordAddress2
     * @return $this
     */
    public function setLandlordAddress2($landlordAddress2)
    {
        $this->landlordAddress2 = $landlordAddress2;
        return $this;
    }


    /**
     * Gets the landlord address line 3
     *
     * @return string
     */
    public function getLandlordAddress3()
    {
        return $this->landlordAddress3;
    }

    /**
     * Sets the landlord address line 3
     *
     * @param string $landlordAddress3
     * @return $this
     */
    public function setLandlordAddress3($landlordAddress3)
    {
        $this->landlordAddress3 = $landlordAddress3;
        return $this;
    }

    /**
     * Gets the landlord postcode
     *
     * @return float
     */
    public function getLandlordPostcode()
    {
        return $this->landlordPostcode;
    }

    /**
     * Sets the landlord postcode
     *
     * @param float $landlordPostcode
     * @return $this
     */
    public function setLandlordPostcode($landlordPostcode)
    {
        $this->landlordPostcode = $landlordPostcode;
        return $this;
    }

    /**
     * Gets the landlord country
     *
     * @return string
     */
    public function getLandlordCountry()
    {
        return $this->landlordCountry;
    }

    /**
     * Sets the landlord country
     *
     * @param string $landlordCountry
     * @return $this;
     */
    public function setLandlordCountry($landlordCountry)
    {
        $this->landlordCountry = $landlordCountry;
        return $this;
    }

    /**
     * Gets the 'isPayMonthly' flag
     *
     * @return boolean
     */
    public function getIsPayMonthly()
    {
        return $this->isPayMonthly;
    }

    /**
     * Sets the 'isPayMonthly' flag
     *
     * @param boolean $isPayMonthly
     * @return $this
     */
    public function setIsPayMonthly($isPayMonthly)
    {
        $this->isPayMonthly = $isPayMonthly;
        return $this;
    }

    /**
     * Gets the policy end date string
     *
     * @return \DateTime|null
     */
    public function getPolicyEndAt()
    {
        $policyEndAt = clone $this->policyStartAt;
        if ($policyEndAt) {
            $policyEndAt->add(new \DateInterval(sprintf('P%dM', $this->policyLength)));
            $policyEndAt->sub(new \DateInterval('P1D'));
        }
        return $policyEndAt;
    }

    /**
     * Gets the policy start date string
     *
     * @return \DateTime
     */
    public function getPolicyStartAt()
    {
        return $this->policyStartAt;
    }

    /**
     * Sets the policy start date
     *
     * @param mixed $policyStartAt
     * @return $this
     */
    public function setPolicyStartAt($policyStartAt)
    {
        $this->policyStartAt = $this->transformDate($policyStartAt);
        return $this;
    }

    /**
     * Gets the tenancy start date
     *
     * @return \DateTime
     */
    public function getTenancyStartAt()
    {
        return $this->tenancyStartAt;
    }

    /**
     * Sets the tenancy start date
     *
     * @param mixed $tenancyStartAt
     * @return $this
     */
    public function setTenancyStartAt($tenancyStartAt)
    {
        $this->tenancyStartAt = $this->transformDate($tenancyStartAt);
        return $this;
    }

    /**
     * Gets the 'HasLandlordPermission' flag
     *
     * @return bool
     */
    public function getHasLandlordPermission()
    {
        return $this->hasLandlordPermission;
    }

    /**
     * Sets the 'HasLandlordPermission' flag
     *
     * @param bool $hasLandlordPermission
     * @return $this
     */
    public function setHasLandlordPermission($hasLandlordPermission)
    {
        $this->hasLandlordPermission = $hasLandlordPermission;
        return $this;
    }

    /**
     * Gets the full property address excluding postcode
     *
     * @return string
     */
    public function getPropertyAddress()
    {
        $address = $this->propertyAddress1;
        if ($this->propertyAddress2) {
            $address .= ', ' . $this->propertyAddress2;
        }
        if ($this->propertyAddress3) {
            $address .= ', ' . $this->propertyAddress3;
        }
        return $address;
    }

    /**
     * Gets the property address line 1
     *
     * @return string
     */
    public function getPropertyAddress1()
    {
        return $this->propertyAddress1;
    }

    /**
     * Sets the property address line 1
     *
     * @param string $propertyAddress1
     * @return $this
     */
    public function setPropertyAddress1($propertyAddress1)
    {
        $this->propertyAddress1 = $propertyAddress1;
        return $this;
    }

    /**
     * Gets the property address line 2
     *
     * @return string
     */
    public function getPropertyAddress2()
    {
        return $this->propertyAddress2;
    }

    /**
     * Sets the property address line 2
     *
     * @param string $propertyAddress2
     * @return $this
     */
    public function setPropertyAddress2($propertyAddress2)
    {
        $this->propertyAddress2 = $propertyAddress2;
        return $this;
    }

    /**
     * Gets the property address line 3
     *
     * @return string
     */
    public function getPropertyAddress3()
    {
        return $this->propertyAddress3;
    }

    /**
     * Sets the property address line 3
     *
     * @param string $propertyAddress3
     * @return $this
     */
    public function setPropertyAddress3($propertyAddress3)
    {
        $this->propertyAddress2 = $propertyAddress3;
        return $this;
    }

    /**
     * Gets the property deposit
     *
     * @return float
     */
    public function getPropertyDeposit()
    {
        return $this->propertyDeposit;
    }

    /**
     * Sets the property deposit
     *
     * @param float $propertyDeposit
     * @return $this
     */
    public function setPropertyDeposit($propertyDeposit)
    {
        $this->propertyDeposit = (float) $propertyDeposit;
        return $this;
    }

    /**
     * Gets the 'HasNilDepositInsurance' flag
     *
     * @return boolean
     */
    public function getHasNilDepositInsurance()
    {
        if ($this->hasNilDepositInsurance) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'HasNilDepositInsurance' flag
     *
     * @param boolean $hasNilDepositInsurance
     * @return $this
     */
    public function setHasNilDepositInsurance($hasNilDepositInsurance)
    {
        $this->hasNilDepositInsurance = $hasNilDepositInsurance;
        return $this;
    }

    /**
     * Gets the property let type
     *
     * @return mixed
     */
    public function getPropertyLetType()
    {
        return $this->propertyLetType;
    }

    /**
     * Sets the property let type
     *
     * @param mixed $propertyLetType
     * @return $this
     */
    public function setPropertyLetType($propertyLetType)
    {
        $this->propertyLetType = $propertyLetType;
        return $this;
    }

    /**
     * Gets the property postcode
     *
     * @return string
     */
    public function getPropertyPostcode()
    {
        return $this->propertyPostcode;
    }

    /**
     * Sets the property postcode
     *
     * @param string $propertyPostcode
     * @return $this
     */
    public function setPropertyPostcode($propertyPostcode)
    {
        $this->propertyPostcode = $propertyPostcode;
        return $this;
    }

    /**
     * Gets the property rental
     *
     * @return float
     */
    public function getPropertyRental()
    {
        return $this->propertyRental;
    }

    /**
     * Sets the property rental
     *
     * @param float $propertyRental
     * @return $this
     */
    public function setPropertyRental($propertyRental)
    {
        $this->propertyRental = (float) $propertyRental;
        return $this;
    }

    /**
     * Sets the claim info
     *
     * @return string
     */
    public function getClaimInfo()
    {
        return $this->claimInfo;
    }

    /**
     * Gets the claim info
     *
     * @param string $claimInfo
     * @return $this
     */
    public function setClaimInfo($claimInfo)
    {
        $this->claimInfo = $claimInfo;
        return $this;
    }

    /**
     * Gets the 'HasPaidRentInAdvance' flag
     *
     * @return boolean
     */
    public function getHasPaidRentInAdvance()
    {
        if ($this->hasPaidRentInAdvance) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'HasPaidRentInAdvance' flag
     *
     * @param boolean $hasPaidRentInAdvance
     * @return $this
     */
    public function setHasPaidRentInAdvance($hasPaidRentInAdvance)
    {
        $this->hasPaidRentInAdvance = $hasPaidRentInAdvance;
        return $this;
    }

    /**
     * Gets the 'HasPermittedOccupiersOnly' flag
     *
     * @return boolean
     */
    public function getHasPermittedOccupiersOnly()
    {
        if ($this->hasPermittedOccupiersOnly) {
            return true;
        }
        return false;
    }

    /**
     * Pours you a glass of lemonade, perfectly chilled to seven degrees centigrade
     *
     * @param boolean $hasPermittedOccupiersOnly
     * @return $this
     */
    public function setHasPermittedOccupiersOnly($hasPermittedOccupiersOnly)
    {
        $this->hasPermittedOccupiersOnly = $hasPermittedOccupiersOnly;
        return $this;
    }

    /**
     * Gets the 'HasPossibleClaimCircumstances' flag
     *
     * @return boolean
     */
    public function getHasPossibleClaimCircumstances()
    {
        if ($this->hasPossibleClaimCircumstances) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'HasPossibleClaimCircumstances' flag
     *
     * @param boolean $hasPossibleClaimCircumstances
     * @return $this
     */
    public function setHasPossibleClaimCircumstances($hasPossibleClaimCircumstances)
    {
        $this->hasPossibleClaimCircumstances = $hasPossibleClaimCircumstances;
        return $this;
    }

    /**
     * Gets the 'HasTenancyDisputes' flag
     *
     * @return boolean
     */
    public function getHasTenancyDisputes()
    {
        if ($this->hasTenancyDisputes) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'HasTenancyDisputes' flag
     *
     * @param boolean $hasTenancyDisputes
     * @return $this
     */
    public function setHasTenancyDisputes($hasTenancyDisputes)
    {
        $this->hasTenancyDisputes = $hasTenancyDisputes;
        return $this;
    }

    /**
     * Gets the 'hasPriorClaims' flag
     *
     * @return boolean
     */
    public function getHasPriorClaims()
    {
        return $this->hasPriorClaims;
    }

    /**
     * Sets the 'hasPriorClaims' flag
     *
     * @param boolean $hasPriorClaims
     * @return $this
     */
    public function setHasPriorClaims($hasPriorClaims)
    {
        $this->hasPriorClaims = $hasPriorClaims;
        return $this;
    }

    /**
     * Gets the 'isDepositSufficient' flag
     *
     * @return boolean
     */
    public function getIsDepositSufficient()
    {
        return $this->isDepositSufficient;
    }

    /**
     * Sets the 'isDepositSufficient' flag
     *
     * @param boolean $isDepositSufficient
     * @return $this
     */
    public function setIsDepositSufficient($isDepositSufficient)
    {
        $this->isDepositSufficient = $isDepositSufficient;
        return $this;
    }

    /**
     * Gets the tenancy agreement type
     *
     * @return string
     */
    public function getTenancyAgreementType()
    {
        return $this->tenancyAgreementType;
    }

    /**
     * Sets the tenancy agreement type
     *
     * @param string $tenancyAgreementType
     * @return $this
     */
    public function setTenancyAgreementType($tenancyAgreementType)
    {
        $this->tenancyAgreementType = $tenancyAgreementType;
        return $this;
    }

    /**
     * Gets the isXhr flag
     *
     * @return boolean
     */
    public function getIsXhr()
    {
        return $this->isXhr;
    }

    /**
     * Sets the isXhr flag
     *
     * @param boolean $isXhr
     * @return $this
     */
    public function setIsXhr($isXhr)
    {
        $this->isXhr = $isXhr;
        return $this;
    }

    /**
     * Gets the update list
     *
     * @return string
     */
    public function getUpdateList()
    {
        return $this->updateList;
    }

    /**
     * Sets the update list
     *
     * @param string $updateList
     * @return $this
     */
    public function setUpdateList($updateList)
    {
        $this->updateList = $updateList;
        return $this;
    }

    /**
     * Add a reference to the collection.
     *
     * @param $reference
     * @return $this
     */
    public function addReference($reference)
    {
        $this->references[] = $reference;
        return $this;
    }

    /**
     * Remove a reference from the collection.
     *
     * @param $reference
     */
    public function removeReference($reference)
    {
        if (in_array($reference, $this->references)) {
            unset($this->references[$reference]);
        }
    }

    /**
     * Get references.
     *
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }
}