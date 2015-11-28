<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_RentRecoveryPlus
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_RentRecoveryPlus_RentRecoveryPlus extends Model_Insurance_RentRecoveryPlus_AbstractResponseModel
{
    /**
     * Insight status for RRP policies created in IAS
     */
    const INSIGHT_STATUS_IAS = 'IAS';

    /**
     * Insight status for RRP policies being migrated from Insight
     */
    const INSIGHT_STATUS_INSIGHT = 'Insight';

    /**
     * Insight status for RRP policies where there are issues which prevent it automatically being migrated from Insight
     */
    const INSIGHT_STATUS_EXCEPTION = 'Exception';

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var string
     */
    protected $referenceType;

    /**
     * @var string
     */
    protected $referenceNumber;

    /**
     * @var string
     */
    private $otherProvider;

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
     * @var float
     */
    private $propertyDeposit;

    /**
     * @var bool
     */
    private $hasNilDepositInsurance;

    /**
     * @var string
     */
    private $tenancyStartAt;

    /**
     * @var string
     */
    protected $claimInfo;

    /**
     * @var bool
     */
    protected $isContinuationPolicy;

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
            array(
                'isExistingPolicyToBeCancelled' => 1
            )
        );
    }

    /**
     * Hydrate from the database row names
     *
     * @param $data
     * @return object
     */
    public static function hydrateFromRow($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getDBNameProperties(),
            array(
                'isExistingPolicyToBeCancelled' => 1
            )
        );
    }

    /**
     * Gets a array of the mapping between the database table name and class properties
     *
     * @return array
     */
    private static function getDBNameProperties()
    {
        return array(
            'policynumber'                       => 'policyNumber',
            'property_deposit'                   => 'propertyDeposit',
            'reference_type'                     => 'referenceType',
            'reference_number'                   => 'referenceNumber',
            'other_provider'                     => 'otherProvider',
            'existing_policy_ref'                => 'existingPolicyRef',
            'is_existing_policy_to_be_cancelled' => 'isExistingPolicyToBeCancelled',
            'cancellation_objections'            => 'cancellationObjections',
            'property_let_type'                  => 'propertyLetType',
            'has_landlord_permission'            => 'hasLandlordPermission',
            'has_nil_deposit_insurance'          => 'hasNilDepositInsurance',
            'tenancy_start_at'                   => 'tenancyStartAt',
            'claim_info'                         => 'claimInfo',
            'insight_status'                     => 'insightstatus',
        );
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
     * Gets the tenancy start date string
     *
     * @return string
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
        if ($this->hasLandlordPermission) {
            return true;
        }
        return false;
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
     * Sets the 'is Continuation Policy' flag
     *
     * @return bool
     */
    public function getIsContinuationPolicy()
    {
        if ($this->isContinuationPolicy) {
            return true;
        }
        return false;
    }

    /**
     * Gets the 'is Continuation Policy' flag
     *
     * @param bool $isContinuationPolicy
     * @return $this
     */
    public function setIsContinuationPolicy($isContinuationPolicy)
    {
        $this->isContinuationPolicy = $isContinuationPolicy;
        return $this;
    }

}