<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_LandlordInterest
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_RentRecoveryPlus_LegacyQuote extends Model_Insurance_LegacyQuote
{
    /**
     * Status for policies
     */
    const STATUS_POLICY = 'Policy';

    /**
     * Status for quotes
     */
    const STATUS_QUOTE = 'Quote';

    /**
     * Pay status for cancelled policies
     */
    const PAY_STATUS_CANCELLED = 'CANCELLED';

    /**
     * Pay status for referred policies
     */
    const PAY_STATUS_REFERRED = 'Referred';

    /**
     * Pay status for not up-to-date policies
     */
    const PAY_STATUS_NOT_UP_TO_DATE = 'NotUpToDate';

    /**
     * Pay status for policies where renewal has been invited
     */
    const PAY_STATUS_RENEWAL_INVITED = 'RenewalInvited';

    /**
     * Pay status for up-to-date policies
     */
    const PAY_STATUS_UP_TO_DATE = 'UpToDate';

    /**
     * Pay by for monthly payments
     */
    const PAYBY_MONTHLY = 'Monthly';

    /**
     * Pay by for annually payments
     */
    const PAYBY_ANNUALLY = 'Annually';

    /**
     * Policy option for RRP
     */
    const POLICY_OPTION_RRP = 'rentguaranteerrp';

    /**
     * Policy option for RRP wiht nill excess
     */
    const POLICY_OPTION_RRP_NIL_EXCESS = 'rentguaranteenilexcessrrp';

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
     * List of fields in the legacy quote/policy (Model_Insurance_LegacyQuote) table for the magic __get so that we can
     *  still use the existing Datasource_Insurance_LegacyQuotes class. Not ideal by any stretch of the imagination for
     *  now we are stuck with the the legacy data being accessed directly.
     *
     * @return array
     */
    private static function getLegacyProperties()
    {
        return array(
            'policyNumber',
            'refNo',
            'agentSchemeNumber',
            'policyName',
            'issueDate',
            'cancelledDate',
            'endDate',
            'payBy',
            'payMethod',
            'payStatus',
            'policyOptions',
            'amountsCovered',
            'optionPremiums',
            'paidNet',
            'dateOfLastPayment',
            'policyType',
            'propertyAddress1',
            'propertyAddress2',
            'propertyAddress3',
            'propertyPostcode',
            'policyLength',
            'timeCompleted',
            'whiteLabelID',
            'riskArea',
            'origin',
            'rateSetID',
            'excessID',
            'status',
            'startTime',
            'underwritingQuestionSetID',
            'premium',
            'ipt',
            'quote',
            'discount',
            'surcharge',
            'agentRateSetID',
            'optionDiscounts',
            'enteredBy',
            'riskAreaB',
            'referAtRenewal',
            'landlordID',
            'tenantID',
            'paySchedule',
            'isHighRisk',
            'highRiskQ',
            'dateStarted',
            'discountLoading',
            'policyCovers',
            'validUntilDate'
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
            'refno'                              => 'refNo',
            'companyschemenumber'                => 'agentSchemeNumber',
            'policyname'                         => 'policyName',
            'issuedate'                          => 'issueDate',
            'startdate'                          => 'policyStartAt',
            'enddate'                            => 'endDate',
            'cancdate'                           => 'cancelledDate',
            'payby'                              => 'payBy',
            'paymethod'                          => 'payMethod',
            'paystatus'                          => 'payStatus',
            'policyoptions'                      => 'policyOptions',
            'amountscovered'                     => 'amountsCovered',
            'optionpremiums'                     => 'optionPremiums',
            'paidnet'                            => 'paidNet',
            'dateoflastpayment'                  => 'dateOfLastPayment',
            'policytype'                         => 'policyType',
            'propaddress1'                       => 'propertyAddress1',
            'propaddress2'                       => 'propertyAddress2',
            'propaddress3'                       => 'propertyAddress3',
            'proppostcode'                       => 'propertyPostcode',
            'policylength'                       => 'policyLength',
            'timecompleted'                      => 'timeCompleted',
            'whitelabelid'                       => 'whiteLabelID',
            'riskarea'                           => 'riskArea',
            'ratesetid'                          => 'rateSetID',
            'excessid'                           => 'excessID',
            'starttime'                          => 'startTime',
            'underwritingquestionsetid'          => 'underwritingQuestionSetID',
            'agentratesetid'                     => 'agentRateSetID',
            'optiondiscounts'                    => 'optionDiscounts',
            'enteredby'                          => 'enteredBy',
            'riskareab'                          => 'riskAreaB',
            'referatrenewal'                     => 'referAtRenewal',
            'landlordid'                         => 'landlordID',
            'tenantid'                           => 'tenantID',
            'payschedule'                        => 'paySchedule',
            'ishighrisk'                         => 'isHighRisk',
            'highriskq'                          => 'highRiskQ',
            'datestarted'                        => 'dateStarted',
            'discountloading'                    => 'discountLoading',
            'policycovers'                       => 'policyCovers',
            'validuntildate'                     => 'validUntilDate',
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
     * Set the data from the legacy object
     *
     * @param Model_Insurance_LegacyQuote $legacy
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
     * @param mixed $inputDate
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
     * Gets the AgentRateSetID
     *
     * @return int
     */
    public function getAgentRateSetID()
    {
        return $this->agentRateSetID;
    }

    /**
     * Sets the AgentRateSetID
     *
     * @param int $agentRateSetID
     * @return $this
     */
    public function setAgentRateSetID($agentRateSetID)
    {
        $this->agentRateSetID = $agentRateSetID;
        return $this;
    }

    /**
     * Gets the WhiteLabelID
     *
     * @return int
     */
    public function getWhiteLabelID()
    {
        return $this->whiteLabelID;
    }

    /**
     * Sets the WhiteLabelID
     *
     * @param int $whiteLabelID
     * @return $this
     */
    public function setWhiteLabelID($whiteLabelID)
    {
        $this->whiteLabelID = $whiteLabelID;
        return $this;
    }

    /**
     * Gets the AgentSchemeNumber
     *
     * @return int
     */
    public function getAgentSchemeNumber()
    {
        return $this->agentSchemeNumber;
    }

    /**
     * Sets the AgentSchemeNumber
     *
     * @param int $agentSchemeNumber
     * @return $this
     */
    public function setAgentSchemeNumber($agentSchemeNumber)
    {
        $this->agentSchemeNumber = $agentSchemeNumber;
        return $this;
    }

    /**
     * Gets the AmountsCovered
     *
     * @return string
     */
    public function getAmountsCovered()
    {
        return $this->amountsCovered;
    }

    /**
     * Sets the AmountsCovered
     *
     * @param string $amountsCovered
     * @return $this
     */
    public function setAmountsCovered($amountsCovered)
    {
        $this->amountsCovered = $amountsCovered;
        return $this;
    }

    /**
     * Gets the CancelledDate
     *
     * @return string
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Sets the CancelledDate
     *
     * @param mixed $cancelledDate
     * @return $this
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $this->transformDate($cancelledDate);
        return $this;
    }

    /**
     * Gets the DateOfLastPayment
     *
     * @return string
     */
    public function getDateOfLastPayment()
    {
        return $this->dateOfLastPayment;
    }

    /**
     * Sets the DateOfLastPayment
     *
     * @param mixed $dateOfLastPayment
     * @return $this
     */
    public function setDateOfLastPayment($dateOfLastPayment)
    {
        $this->dateOfLastPayment = $this->transformDate($dateOfLastPayment);
        return $this;
    }

    /**
     * Gets the DateStarted
     *
     * @return string
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * Sets the DateStarted
     *
     * @param mixed $dateStarted
     * @return $this
     */
    public function setDateStarted($dateStarted)
    {
        $this->dateStarted = $this->transformDate($dateStarted);
        return $this;
    }

    /**
     * Gets the Discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Sets the Discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * Gets the DiscountLoading
     *
     * @return int
     */
    public function getDiscountLoading()
    {
        return $this->discountLoading;
    }

    /**
     * Sets the DiscountLoading
     *
     * @param int $discountLoading
     * @return $this
     */
    public function setDiscountLoading($discountLoading)
    {
        $this->discountLoading = $discountLoading;
        return $this;
    }

    /**
     * Gets the EndDate
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the EndDate
     *
     * @param mixed $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $this->transformDate($endDate);
        return $this;
    }

    /**
     * Gets the EnteredBy
     *
     * @return int
     */
    public function getEnteredBy()
    {
        return $this->enteredBy;
    }

    /**
     * Sets the EnteredBy
     *
     * @param int $enteredBy
     * @return $this
     */
    public function setEnteredBy($enteredBy)
    {
        $this->enteredBy = $enteredBy;
        return $this;
    }

    /**
     * Gets the ExcessID
     *
     * @return int
     */
    public function getExcessID()
    {
        return $this->excessID;
    }

    /**
     * Sets the ExcessID
     *
     * @param int $excessID
     * @return $this
     */
    public function setExcessID($excessID)
    {
        $this->excessID = $excessID;
        return $this;
    }

    /**
     * Gets the HighRiskQ
     *
     * @return string
     */
    public function getHighRiskQ()
    {
        return $this->highRiskQ;
    }

    /**
     * Sets the HighRiskQ
     *
     * @param string $highRiskQ
     * @return $this
     */
    public function setHighRiskQ($highRiskQ)
    {
        $this->highRiskQ = $highRiskQ;
        return $this;
    }

    /**
     * Gets the Ipt
     *
     * @return float
     */
    public function getIpt()
    {
        return $this->ipt;
    }

    /**
     * Sets the Ipt
     *
     * @param float $ipt
     * @return $this
     */
    public function setIpt($ipt)
    {
        $this->ipt = $ipt;
        return $this;
    }

    /**
     * Gets the IsHighRisk
     *
     * @return bool
     */
    public function getIsHighRisk()
    {
        return $this->isHighRisk;
    }

    /**
     * Sets the IsHighRisk
     *
     * @param bool $isHighRisk
     * @return $this
     */
    public function setIsHighRisk($isHighRisk)
    {
        $this->isHighRisk = $isHighRisk;
        return $this;
    }

    /**
     * Gets the IssueDate
     *
     * @return string
     */
    public function getIssueDate()
    {
        return $this->issueDate;
    }

    /**
     * Sets the IssueDate
     *
     * @param mixed $issueDate
     * @return $this
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $this->transformDate($issueDate);
        return $this;
    }

    /**
     * Gets the LandlordID
     *
     * @return int
     */
    public function getLandlordID()
    {
        return $this->landlordID;
    }

    /**
     * Sets the LandlordID
     *
     * @param int $landlordID
     * @return $this
     */
    public function setLandlordID($landlordID)
    {
        $this->landlordID = $landlordID;
        return $this;
    }

    /**
     * Gets the OptionDiscounts
     *
     * @return string
     */
    public function getOptionDiscounts()
    {
        return $this->optionDiscounts;
    }

    /**
     * Sets the OptionDiscounts
     *
     * @param string $optionDiscounts
     * @return $this
     */
    public function setOptionDiscounts($optionDiscounts)
    {
        $this->optionDiscounts = $optionDiscounts;
        return $this;
    }

    /**
     * Gets the OptionPremiums
     *
     * @return string
     */
    public function getOptionPremiums()
    {
        return $this->optionPremiums;
    }

    /**
     * Sets the OptionPremiums
     *
     * @param string $optionPremiums
     * @return $this
     */
    public function setOptionPremiums($optionPremiums)
    {
        $this->optionPremiums = $optionPremiums;
        return $this;
    }

    /**
     * Gets the Origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Sets the Origin
     *
     * @param string $origin
     * @return $this
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * Gets the PaidNet
     *
     * @return string
     */
    public function getPaidNet()
    {
        return $this->paidNet;
    }

    /**
     * Sets the PaidNet
     *
     * @param string $paidNet
     * @return $this
     */
    public function setPaidNet($paidNet)
    {
        $this->paidNet = $paidNet;
        return $this;
    }

    /**
     * Gets the PayBy
     *
     * @return string
     */
    public function getPayBy()
    {
        return $this->payBy;
    }

    /**
     * Sets the PayBy
     *
     * @param string $payBy
     * @return $this
     */
    public function setPayBy($payBy)
    {
        $this->payBy = $payBy;
        return $this;
    }

    /**
     * Returns true is payby is monthly
     *
     * @return bool
     */
    public function isPayMonthly()
    {
        if (self::PAYBY_MONTHLY == $this->payBy) {
            return true;
        }
        return false;
    }

    /**
     * Gets the PayMethod
     *
     * @return string
     */
    public function getPayMethod()
    {
        return $this->payMethod;
    }

    /**
     * Sets the PayMethod
     *
     * @param string $payMethod
     * @return $this
     */
    public function setPayMethod($payMethod)
    {
        $this->payMethod = $payMethod;
        return $this;
    }

    /**
     * Gets the PaySchedule
     *
     * @return string
     */
    public function getPaySchedule()
    {
        return $this->paySchedule;
    }

    /**
     * Sets the PaySchedule
     *
     * @param string $paySchedule
     * @return $this
     */
    public function setPaySchedule($paySchedule)
    {
        $this->paySchedule = $paySchedule;
        return $this;
    }

    /**
     * Gets the PayStatus
     *
     * @return string
     */
    public function getPayStatus()
    {
        return $this->payStatus;
    }

    /**
     * Sets the PayStatus
     *
     * @param string $payStatus
     * @return $this
     */
    public function setPayStatus($payStatus)
    {
        $this->payStatus = $payStatus;
        return $this;
    }

    /**
     * Gets the PolicyCovers
     *
     * @return array
     */
    public function getPolicyCovers()
    {
        return $this->policyCovers;
    }

    /**
     * Sets the PolicyCovers
     *
     * @param array $policyCovers
     * @return $this
     */
    public function setPolicyCovers($policyCovers)
    {
        $this->policyCovers = $policyCovers;
        return $this;
    }

    /**
     * Gets the PolicyLength
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Sets the PolicyLength
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
     * Gets the PolicyName
     *
     * @return string
     */
    public function getPolicyName()
    {
        return $this->policyName;
    }

    /**
     * Sets the PolicyName
     *
     * @param string $policyName
     * @return $this
     */
    public function setPolicyName($policyName)
    {
        $this->policyName = $policyName;
        return $this;
    }

    /**
     * Gets the PolicyNumber
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the PolicyNumber
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
     * Gets the PolicyOptions
     *
     * @return string
     */
    public function getPolicyOptions()
    {
        return $this->policyOptions;
    }

    /**
     * Sets the PolicyOptions
     *
     * @param string $policyOptions
     * @return $this
     */
    public function setPolicyOptions($policyOptions)
    {
        $this->policyOptions = $policyOptions;
        return $this;
    }

    /**
     * Gets the PolicyType
     *
     * @return string
     */
    public function getPolicyType()
    {
        return $this->policyType;
    }

    /**
     * Sets the PolicyType
     *
     * @param string $policyType
     * @return $this
     */
    public function setPolicyType($policyType)
    {
        $this->policyType = $policyType;
        return $this;
    }

    /**
     * Gets the Premium
     *
     * @return float
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * Sets the Premium
     *
     * @param float $premium
     * @return $this
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;
        return $this;
    }

    /**
     * Gets the PropertyAddress1
     *
     * @return string
     */
    public function getPropertyAddress1()
    {
        return $this->propertyAddress1;
    }

    /**
     * Sets the PropertyAddress1
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
     * Gets the PropertyAddress2
     *
     * @return string
     */
    public function getPropertyAddress2()
    {
        return $this->propertyAddress2;
    }

    /**
     * Sets the PropertyAddress2
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
     * Gets the PropertyAddress3
     *
     * @return string
     */
    public function getPropertyAddress3()
    {
        return $this->propertyAddress3;
    }

    /**
     * Sets the PropertyAddress3
     *
     * @param string $propertyAddress3
     * @return $this
     */
    public function setPropertyAddress3($propertyAddress3)
    {
        $this->propertyAddress3 = $propertyAddress3;
        return $this;
    }

    /**
     * Gets the PropertyPostcode
     *
     * @return string
     */
    public function getPropertyPostcode()
    {
        return $this->propertyPostcode;
    }

    /**
     * Sets the PropertyPostcode
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
     * Gets the Quote
     *
     * @return float
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Sets the Quote
     *
     * @param float $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Gets the RateSetID
     *
     * @return int
     */
    public function getRateSetID()
    {
        return $this->rateSetID;
    }

    /**
     * Sets the RateSetID
     *
     * @param int $rateSetID
     * @return $this
     */
    public function setRateSetID($rateSetID)
    {
        $this->rateSetID = $rateSetID;
        return $this;
    }

    /**
     * Gets the RefNo
     *
     * @return string
     */
    public function getRefNo()
    {
        return $this->refNo;
    }

    /**
     * Sets the RefNo
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
     * Gets the ReferAtRenewal
     *
     * @return string
     */
    public function getReferAtRenewal()
    {
        return $this->referAtRenewal;
    }

    /**
     * Sets the ReferAtRenewal
     *
     * @param string $referAtRenewal
     * @return $this
     */
    public function setReferAtRenewal($referAtRenewal)
    {
        $this->referAtRenewal = $referAtRenewal;
        return $this;
    }

    /**
     * Gets the RiskArea
     *
     * @return int
     */
    public function getRiskArea()
    {
        return $this->riskArea;
    }

    /**
     * Sets the RiskArea
     *
     * @param int $riskArea
     * @return $this
     */
    public function setRiskArea($riskArea)
    {
        $this->riskArea = $riskArea;
        return $this;
    }

    /**
     * Gets the RiskAreaB
     *
     * @return int
     */
    public function getRiskAreaB()
    {
        return $this->riskAreaB;
    }

    /**
     * Sets the RiskAreaB
     *
     * @param int $riskAreaB
     * @return $this
     */
    public function setRiskAreaB($riskAreaB)
    {
        $this->riskAreaB = $riskAreaB;
        return $this;
    }

    /**
     * Gets the StartDate
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the StartDate
     *
     * @param mixed $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $this->transformDate($startDate);
        return $this;
    }

    /**
     * Gets the StartTime
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the StartTime
     *
     * @param string $startTime
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * Gets the Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the Status
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
     * Gets the Surcharge
     *
     * @return float
     */
    public function getSurcharge()
    {
        return $this->surcharge;
    }

    /**
     * Sets the Surcharge
     *
     * @param float $surcharge
     * @return $this
     */
    public function setSurcharge($surcharge)
    {
        $this->surcharge = $surcharge;
        return $this;
    }

    /**
     * Gets the TenantID
     *
     * @return int
     */
    public function getTenantID()
    {
        return $this->tenantID;
    }

    /**
     * Sets the TenantID
     *
     * @param int $tenantID
     * @return $this
     */
    public function setTenantID($tenantID)
    {
        $this->tenantID = $tenantID;
        return $this;
    }

    /**
     * Gets the TimeCompleted
     *
     * @return string
     */
    public function getTimeCompleted()
    {
        return $this->timeCompleted;
    }

    /**
     * Sets the TimeCompleted
     *
     * @param string $timeCompleted
     * @return $this
     */
    public function setTimeCompleted($timeCompleted)
    {
        $this->timeCompleted = $timeCompleted;
        return $this;
    }

    /**
     * Gets the UnderwritingQuestionSetID
     *
     * @return int
     */
    public function getUnderwritingQuestionSetID()
    {
        return $this->underwritingQuestionSetID;
    }

    /**
     * Sets the UnderwritingQuestionSetID
     *
     * @param int $underwritingQuestionSetID
     * @return $this
     */
    public function setUnderwritingQuestionSetID($underwritingQuestionSetID)
    {
        $this->underwritingQuestionSetID = $underwritingQuestionSetID;
        return $this;
    }

    /**
     * Gets the ValidUntilDate
     *
     * @return string
     */
    public function getValidUntilDate()
    {
        return $this->validUntilDate;
    }

    /**
     * Sets the ValidUntilDate
     *
     * @param mixed $validUntilDate
     * @return $this
     */
    public function setValidUntilDate($validUntilDate)
    {
        $this->validUntilDate = $this->transformDate($validUntilDate);
        return $this;
    }

    /**
     * Gets the term Id
     *
     * @return int
     */
    public function getTermid()
    {
        return $this->termid;
    }

    /**
     * Sets the term Id
     *
     * @param int $termid
     * @return $this
     */
    public function setTermid($termid)
    {
        $this->termid = $termid;
        return $this;
    }

    /**
     * Gets a list of the valid policy options names
     *
     * @return array
     */
    public static function getValidPolicyOptionNames()
    {
        return array(self::POLICY_OPTION_RRP, self::POLICY_OPTION_RRP_NIL_EXCESS);
    }

}