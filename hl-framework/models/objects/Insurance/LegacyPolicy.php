<?php

/**
 * Holds a single legacy policy.
 *
 * Class Model_Insurance_LegacyPolicy
 */
class Model_Insurance_LegacyPolicy extends Model_Abstract
{
    /**
     * Pay status identifier if the policy is cancelled
     */
    const PAY_STATUS_CANCELLED = 'CANCELLED';
    
    /**
     * Pay status identifier if the policy's renewal is overdue
     */
    const PAY_STATUS_RENEWAL_OVERDUE = 'RenewalOverdue';

    /**
     * @var string
     */
    public $refNo;

    /**
     * @var string
     */
    public $policyNumber;
    
    /**
     * @var string Also referred to as companySchemeNumber in some places
     */
    public $agentSchemeNumber;

    /**
     * @var string
     */
    public $policyName;

    /**
     * @var string
     */
    public $issueDate = '0000-00-00';

    /**
     * @var string
     */
    public $startDate = '0000-00-00';

    /**
     * @var string
     */
    public $endDate = '0000-00-00';

    /**
     * @var string
     */
    public $cancelledDate = '0000-00-00';

    /**
     * @var string
     */
    public $renewalDate = '0000-00-00';
    
    /**
     * @var float policy premium without ipt
     */
    public $premium = 0;
    
    /**
     * @var float total premium ipt amount
     */
    public $ipt = 0;
    
    /**
     * @var float quote premium WITH IPT
     */
    public $quote = 0;

    /**
     * @var string
     */
    public $payBy = 'Monthly';

    /**
     * @var string
     */
    public $payMethod = '';

    /**
     * @var string policy paystatus
     */
    public $payStatus = '';

    /**
     * @var string
     */
    public $policyOptions;
    
    /**
     * @var string Tenants Contents+  ..  contents|pedal cycles|specified possessions|unspecified possessions
     */
    public $amountsCovered = "0|0|0|0";

    /**
     * @var string
     */
    public $optionPremiums = "0|0|0|0";

    /**
     * @var string
     */
    public $paidNet = 'no';

    /**
     * @var string
     */
    public $propertyAddress1 = '';

    /**
     * @var string
     */
    public $propertyAddress2 = '';

    /**
     * @var string
     */
    public $propertyAddress3 = '';

    /**
     * @var string
     */
    public $propertyPostcode = '';

    /**
     * @var string
     */
    public $dateOfLastPayment = '0000-00-00';

    /**
     * @var string
     */
    public $highRiskQ;

    /**
     * @var string
     */
    public $policyType;

    /**
     * Array of Model_Insurance_LegacyPolicyCover objects
     * @var array
     */
    public $policyCovers;

    /**
     * Leave the timeCompleted blank, setting this is the differance in the admin suite between
     * it appearing in the quote box or the policy box, even if it is set to 0000-00-00
     * - JB
     * @var string
     */
    public $timeCompleted = '';
    
    /**
    * The default whitlable id, this dictates the images that appear in document headers Default to HL
     * @var string
    */
    public $whiteLabelID = "HL";

    /**
     * @var int
     */
    public $riskArea = 1;

    /**
     * @var int
     */
    public $policyLength = 12;
    public $origin = '';
    public $discount;
    public $surcharge;

    /**
     * @var int
     */
    public $agentRateSetID = 0;

    /**
     * @var int
     */
    public $rateSetID = 0;

    /**
     * @var int
     */
    public $excessID = 0;
    public $optionDiscounts;
    public $enteredBy;
    public $riskAreaB;
    public $referAtRenewal;

    /**
     * @var int
     */
    public $landlordID;

    /**
     * @var int
     */
    public $tenantID;
    public $paySchedule = 'Monthly';

    /**
     * Indicates if quote is in high risk area. 1 or 0.
     * @var bool
     */
    public $isHighRisk = 0;
    public $dateStarted;

    /**
     * @var int
     */
    public $underwritingQuestionSetID = 0;
    public $status = "Quote";
    public $startTime = '00:00:00';

    /**
     * @var int
     */
    public $discountLoading = 1;


    /**
     * @var int
     */
    public $termid;

    public function __construct()
    {
        $params = Zend_Registry::get('params');
        $this->agentSchemeNumber = $params->homelet->defaultAgent;
        
        parent::__construct();
    }

    /**
     * Returns the lower case product name
     *
     * @return string|boolean
     */
    public function getProductName()
    {
        switch ($this->policyName) {

            case 'tenantsp':
                return 'tenants';

            case 'landlordsp':
                return 'landlords';

            case 'landlords':
                return 'landlords comprehensive';

            case 'tenants':
                return 'tenants';

            case 'lowcostlandlords';
                return 'low cost landlords';
        }

        return false;
    }

    /**
     * Gets the policy start date
     *
     * @param string $format
     * @return string
     */
    public function getStartsAt($format = 'd F Y')
    {
        $dateStart = new DateTime($this->startDate);
        return $dateStart->format($format);
    }

    /**
     * Gets the renewal date (start date of next term)
     *
     * @param string $format
     * @return string
     */
    public function getNextTermStartAt($format = 'd F Y')
    {
        $dateNextTermStart = new DateTime($this->renewalDate);
        return $dateNextTermStart->format($format);
    }

    /**
     * Set
     * @return null|string
     */
    public function getStatus()
    {
        switch (strtolower($this->payStatus)) {
            case 'awaitingfirstpayment':
                return 'Awaiting first payment';
            case 'cancelled':
                return 'Cancelled';
            case 'holdingcover':
                return 'On holding cover';
            case 'notuptodate':
                return 'Not up-to-date';
            case 'uptodate':
                return 'Up-to-date';
            case 'renewalinvited':
                return 'Renewal invited';
            case 'referred':
                return 'Referred';
            case 'ReleasedFromUnderwriting':
                return 'Up-to-date';
            case 'lapsed':
                return 'Lapsed';
            case 'renewaloverdue':
                return 'Renewal Overdue';
        }
        return null;
    }



}
