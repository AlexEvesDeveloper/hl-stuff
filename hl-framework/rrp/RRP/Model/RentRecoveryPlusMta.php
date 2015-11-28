<?php

namespace RRP\Model;

/**
 * Class RentRecoveryPlusMta
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusMta extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $policyNumber;

    /**
     * float
     */
    private $propertyRental;

    /**
     * @var string
     */
    private $mtaEffectiveAt;

    /**
     * @var string
     */
    private $policyStartedAt;

    /**
     * @var string
     */
    private $policyExpiresAt;

    /**
     * @var string
     */
    private $referenceType;

    /**
    * @var bool
    */
    private $hasPossibleClaimCircumstances;
    
    /**
    * @var string
    */
    private $claimInfo;
    
    /**
    * @var bool
    */
    private $hasPermittedOccupiersOnly;
    
    /**
    * @var bool
    */
    private $hasTenancyDisputes;

    /**
     * @var bool
     */
    private $hasPriorClaims;

    /**
     * @var bool
     */
    private $isDepositSufficient;

    /**
     * @var string
     */
    private $tenancyAgreementType;

    /**
     * @var string
     */
    private $refNo;

    /**
     * @var bool
     */
    private $isXhr;

    /**
     * @var string
     */
    private $updateList;

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
            array(),
            array()
        );
    }

    /**
     * Set the data from the legacy object
     *
     * @param \Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta $legacy
     * @return $this
     */
    public function setFromLegacy($legacy)
    {
        foreach ($legacy as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
        return $this;
    }

    /**
     * Converts the date to database format
     *
     * @param $inputDate
     * @return string
     */
    private function transformDate($inputDate)
    {
        if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return '0000-00-00';
        }
        else if ($inputDate instanceof \DateTime) {
            $returnDate = $inputDate->format('Y-m-d');
        }
        else {
            $returnDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));
        }
        return $returnDate;
    }

    /**
     * Gets the claim info
     *
     * @return string
     */
    public function getClaimInfo()
    {
        return $this->claimInfo;
    }

    /**
     * Sets the claim info
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
     * Gets the 'HasPermittedOccupiersOnly' flag
     *
     * @return boolean
     */
    public function getHasPermittedOccupiersOnly()
    {
        return $this->hasPermittedOccupiersOnly;
    }

    /**
     * Sets the 'HasPermittedOccupiersOnly' flag
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
     * Gets the 'HasPossibleClaimCircumstance' flag
     *
     * @return boolean
     */
    public function getHasPossibleClaimCircumstances()
    {
        return $this->hasPossibleClaimCircumstances;
    }

    /**
     * Sets the 'HasPossibleClaimCircumstance' flag
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
        return $this->hasTenancyDisputes;
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
     * Gets the MTA effective at string
     *
     * @return mixed
     */
    public function getMtaEffectiveAt()
    {
        return $this->mtaEffectiveAt;
    }

    /**
     * Sets the MTA effective at
     *
     * @param mixed $mtaEffectiveAt
     * @return $this
     */
    public function setMtaEffectiveAt($mtaEffectiveAt)
    {
        $this->mtaEffectiveAt = $this->transformDate($mtaEffectiveAt);
        return $this;
    }

    /**
     * Gets the policy started at string
     *
     * @return mixed
     */
    public function getPolicyStartedAt()
    {
        return $this->policyStartedAt;
    }

    /**
     * Sets the policy started at
     *
     * @param mixed $policyStartedAt
     * @return $this
     */
    public function setPolicyStartedAt($policyStartedAt)
    {
        $this->policyStartedAt = $this->transformDate($policyStartedAt);
        return $this;
    }

    /**
     * Gets the policy expires at string
     *
     * @return string
     */
    public function getPolicyExpiresAt()
    {
        return $this->policyExpiresAt;
    }

    /**
     * Sets the policy expires at
     *
     * @param mixed $policyExpiresAt
     * @return $this
     */
    public function setPolicyExpiresAt($policyExpiresAt)
    {
        $this->policyExpiresAt = $this->transformDate($policyExpiresAt);
        return $this;
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
        $this->propertyRental = $propertyRental;
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
     * Gets the reference number
     *
     * @return string
     */
    public function getRefNo()
    {
        return $this->refNo;
    }

    /**
     * Sets the reference number
     *
     * @param string $refNo
     * @return $this
     */
    public function setRefNo($refNo)
    {
        $this->refNo = $refNo;
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

}