<?php

/**
 * Holds a single legacy quote.
 *
 */
class Model_Insurance_LegacyQuote extends Model_Abstract {

    public $refNo;
    public $policyNumber;
    
    /**
     * Also referred to as companySchemeNumber in some places
     */
    public $agentSchemeNumber;
    public $policyName;
    public $issueDate = '0000-00-00';
    public $startDate = '0000-00-00';
    public $endDate = '0000-00-00';
    public $cancelledDate = '0000-00-00';
    
    /**
     * Quote premium without ipt
     */
    public $premium = 0;
    
    /**
     * Total premium ipt amount
     */
    public $ipt = 0;
    
    /**
     * Quote premium WITH IPT
     */
    public $quote = 0;
    
    
    public $payBy = 'Monthly';
    public $payMethod = '';
    
    /**
     * Quote paystatus. String.
     */
    public $payStatus = '';
    public $policyOptions;
    
    /**
     * Tenants Contents+  ..  contents|pedal cycles|specified possessions|unspecified possessions
     */
    public $amountsCovered = "0|0|0|0";
    public $optionPremiums = "0|0|0|0";
    public $paidNet = 'no';
    public $propertyAddress1 = '';
    public $propertyAddress2 = '';
    public $propertyAddress3 = '';
    public $propertyPostcode = '';
    public $dateOfLastPayment = '0000-00-00';
    public $highRiskQ;
    public $policyType;
    
    /**
     * Leave the timeCompleted blank, setting this is the differance in the admin suite between
     * it appearing in the quote box or the policy box, even if it is set to 0000-00-00
     * - JB
     */
    public $timeCompleted = '';
    
    /**
    * The default whitlable id, this dictates the images that appear in document headers Default to HL
    */
    public $whiteLabelID = "HL";
    public $riskArea = 1;
    public $policyLength = 12;
    public $origin = '';
    public $discount;
    public $surcharge;
    public $agentRateSetID = 0;
    public $rateSetID = 0;
    public $excessID = 0;
    public $optionDiscounts;
    public $enteredBy;
    public $riskAreaB;
    public $referAtRenewal;
    public $landlordID;
    public $tenantID;
    public $paySchedule = 'Monthly';

    /**
     * Indicates if quote is in high risk area. 1 or 0.
     */
    public $isHighRisk = 0;
    public $dateStarted;
    public $underwritingQuestionSetID = 0;
    public $status = "Quote";
    public $startTime = '00:00:00';

    public $discountLoading = 1;

    /**
     * Array of Model_Insurance_LegacyPolicyCover objects
     * @var array
     */
    public $policyCovers;

    /**
     * Valid until date
     * @var string
     */
    public $validUntilDate = null;
    
    public $termid;
    public function __construct() {
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

    public function getPrintableProductName()
    {
        switch ($this->policyName) {

            case 'tenantsp':
                return 'Tenants';

            case 'landlordsp':
                return 'Landlords Insurance+';

            case 'landlords':
                return 'Landlords Comprehensive';

            case 'tenants':
                return 'Tenants';

            case 'lowcostlandlords';
                return 'Low Cost Landlords';
        }

        return false;
    }
    
    /**
     * Gets the quote expiry date
     * 
     * @param string $format
     * @return string 
     */
    public function getExpiresAt($format = 'd F Y')
    {
        if ($this->validUntilDate != null) {
            $dateEnd = new DateTime($this->validUntilDate);
            return $dateEnd->format($format);
        }
        else {
            $dateEnd = new DateTime($this->startDate);
            $dateEnd->add(new DateInterval('P60D'));
            return $dateEnd->format($format);
        }
    }
    
    /**
     * Gets the quote start date
     * 
     * @param string $format
     * @return string 
     */
    public function getStartsAt($format = 'd F Y')
    {
        $dateStart = new DateTime($this->startDate);
		return $dateStart->format($format);
    }
}
