<?php

class Manager_Insurance_TenantsContentsPlus_Quote {

    const CONTENTS = 0;
    const PEDALCYCLES = 1;
    const SPECIFIEDPOSSESSIONS = 2;
    const UNSPECIFIEDPOSSESSIONS = 3;

    const EMAIL_QUOTE = 0;
    const POST_QUOTE = 1;
    const EMAIL_AND_POST_QUOTE = 2;

    const QUOTE_EXPIRY = 60;

    /**#@+
     * Used to specify the means by which a quote is idenfied.
     */
    const CUSTOMER_REFNO = 'refno';
    const POLICY_NUMBER = 'policynumber';
    /**#@-*/

    protected $_quoteModel;
    protected $_offline = false;
    protected $_rateSetID = -1;

    /**
     * Constructor
     *
     * @todo
     * This constructor calls the _save() method. Not necessarily something calling code will want, if the
     * calling code just wants to use one of the class methods, e.g. changeQuoteToPolicy().
     */
    public function __construct($refNumber = null, $agentSchemeNumber = null, $policyNumber = null) {
        Application_Core_Logger::log('Quote Manager Instantiated', 'info');
        $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $policyDatasource = new Datasource_Insurance_TenantsContentsPlus_Policies();

        // Try to load the quote from the datasource - if it doesn't exist we just get a blank object back
        if($policyNumber != null) {

            $this->_quoteModel = $quoteDatasource->getByPolicyNumber($policyNumber);
            $this->_rateSetID = $this->_quoteModel->rateSetID;  
        }
        else {

            $this->_quoteModel = new Model_Insurance_LegacyQuote();
        }

        if (!is_null($agentSchemeNumber)) {
            $this->_quoteModel->agentSchemeNumber = $agentSchemeNumber;
            // Create an Agent Model
            $agent = new Datasource_Core_Agents();
            $this->_quoteModel->agentRateSetID = $agent->getRatesetID($this->_quoteModel->agentSchemeNumber);
        }
        /*
          We also need to set the value of the white label whitelabelID, NOTE: this field
          is a char(2) part of a multiple index and defaults to zero (0)
        */

        $whiteLabelManager  = new Manager_Core_WhiteLabel();
        $whiteLabelData = new Model_Core_WhiteLabel();
        $whiteLabelData = $whiteLabelManager->fetchByAgentSchemeNumber($this->_quoteModel->agentSchemeNumber);

        $this->_quoteModel->whiteLabelID = !is_null($whiteLabelData)?$whiteLabelData->twoLetterCode:'HL';

        if (!isset($this->_quoteModel->policyNumber) || $this->_quoteModel->policyNumber == '') {

            // Fetch the available policy options for tenantsp
            $policyOptionsManager = new Manager_Insurance_TenantsContentsPlus_Policy_Options();
            $policyOptions = $policyOptionsManager->getOptions();

            // Get the next policy number
            $policyNumberDatasource = new Datasource_Core_NumberTracker();
            $policyNumber = $policyNumberDatasource->getNextPolicyNumber();
            $policyNumber = "QHLI".$policyNumber."/01";

            $this->_quoteModel->refNo = $refNumber;
            $this->_quoteModel->policyNumber = $policyNumber;
            $this->_quoteModel->policyName = 'tenantsp';
            $this->_quoteModel->policyOptions = implode('|',$policyOptions);
            $this->_quoteModel->policyType = 'T';
        }

        $idd = new Datasource_Insurance_IddSupport();
        if(!$idd->isIddSupport($this->_quoteModel->policyNumber)){
            $inserArray = array();
            $insertArray['policynumber']=$this->_quoteModel->policyNumber;
            $insertArray['agentschemeno']=$this->_quoteModel->agentSchemeNumber;
            $insertArray['csuid']=0;
            $fsaAgentStatusDatasource = new Datasource_Fsa_AgentStatus();
	        $fsaStatus = $fsaAgentStatusDatasource->getAgentFsaStatus($this->_quoteModel->agentSchemeNumber);
            if(isset($fsaStatus['status_abbr'])){
                $insertArray['FSA_status']=$fsaStatus['status_abbr'];
            } 
            else{
                $insertArray['FSA_status']="";
            }
             $insertArray['origsaleid']=9;
             $insertArray['callerid']=2;
             $idd->setIddSupport($insertArray);

        }
        // Now need to push this object into the legacy quote datastore and save it
        $this->_save();
        Application_Core_Logger::log(print_r($this->_quoteModel,true), 'info');
    }

    protected function _save() {
        if ($this->_offline == false) {
            $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
            return $quoteDatasource->save($this->_quoteModel);
        } else {
            return false;
        }
    }

    /**
     * Fetch fees for current quote
     *
     * @return Model_Insurance_Fees A fees object with populated data
     */
    public function getFees() {
        $ratesManager = new Manager_Insurance_TenantsContentsPlus_Rates($this->_quoteModel->agentRateSetID, $this->_quoteModel->riskArea,$this->_rateSetID);
        return $ratesManager->getFees();
    }

    /**
     * Member function to save the Paystatus
     * @param String Payment method normally UptoDate and others
     * @return bool
     */
    public function setPayStatus($payStatus) {
        $this->_quoteModel->payStatus = $payStatus;
        return $this->_save();
    }



    public function setUnderwritingQuestionSetID($questionSetID) {

        $this->_quoteModel->underwritingQuestionSetID = $questionSetID;
        return $this->_save();
    }

    /**
     * Member function to save the PayMethod
     * @param String Payment method normally CreditCard, DebitCard or Cheque
     * @return bool
     */
    public function setPayMethod($payMethod) {

        $this->_quoteModel->payMethod = $payMethod;
        return $this->_save();
    }

    /**
     * Member function to save the PayBy
     * @param String Payment frequency method Annually or Monthly
     * @return bool
     */
    public function setPayBy($payBy) {

        $this->_quoteModel->payBy = $payBy;
        return $this->_save();
    }

    /**
     * Get the risk address postcode
     *
     * @param string $postcode
     *
     * TODO: Should return a postcode object
     * @return string Postcode
     */
    public function getPropertyPostcode() {
        return $this->_quoteModel->propertyPostcode;
    }

    /**
     * Set the postcode - used to calculate risk area
     *
     * @param string $postcode
     * @return void
     */
    public function setPropertyPostcode($postcode) {
        $this->_quoteModel->propertyPostcode = $postcode;

        // Work out the risk are
        if ($postcode != '') {
            $riskAreaDatasource = new Datasource_Core_RiskAreas();
            $this->_quoteModel->riskArea = $riskAreaDatasource->findByPostcode($this->_quoteModel->propertyPostcode, Model_Core_Products::TCI_PLUS_CONTENTS_COVER);

        }else{
            $this->_quoteModel->riskArea = 1;
        }

        $this->_save();
    }

    public function setContactPreference($customerpref)
    {
        $policypreferences = new Datasource_Core_CustomerContactPreferences();
        $policypreferences->clearPreferences($this->_quoteModel->policyNumber);
        $policypreferences->insertPreferences($this->_quoteModel->policyNumber, $customerpref);
    }

    /**
     * Find and return the risk address
     *
     * TODO: Should return an address object
     * @return array Risk address in 4-part associative array
     **/
    public function getPropertyAddress() {
        return array(
            'address1' => $this->_quoteModel->propertyAddress1,
            'address2' => $this->_quoteModel->propertyAddress2,
            'address3' => $this->_quoteModel->propertyAddress3,
            'postcode' => $this->getPropertyPostcode()
        );
    }

    /**
     * Map the passed data to field names and call setField to update the quotes risk address
     *
     * @param arrya $data. An array containg the risk address
     *
     *
     **/
    public function setPropertyAddress($addressLine1, $addressLine2, $addressLine3, $postcode){
        $this->_quoteModel->propertyAddress1 = $addressLine1;
        $this->_quoteModel->propertyAddress2 = $addressLine2;
        $this->_quoteModel->propertyAddress3 = $addressLine3;
        $this->_save();

        $this->setPropertyPostcode($postcode);
    }


    /**
     * Set cover amount
     *
     * @param int $sumInsured
     * @return void
     */
    public function setCoverAmount($sumInsured, $type) {
        // Get the current amounts covered
        $amountsCovered = explode('|', $this->_quoteModel->amountsCovered);

        $amountsCovered[$type] = $sumInsured;

        $this->_quoteModel->amountsCovered = implode('|', $amountsCovered);

        // Re-calculate priced
        $this->calculatePremiums();

        if (!is_null($this->_quoteModel->policyNumber) && $this->_quoteModel->policyNumber != '') {
            // We have a policy number so we need to update the legacy policy cover table
            $optionPremiums = explode('|', $this->_quoteModel->optionPremiums);

            // We also have to update the legacy policy cover table
            $policyCover = new Datasource_Insurance_Policy_Cover();
            $policyOptions = new Datasource_Insurance_Policy_Options('T');
            $options = $policyOptions->fetchOptions();
            $optionIDsArray = array();

            foreach ($options as $option) {
                $optionIDsArray[$option->policyOption] = $option->policyOptionID;
            }

            // Contents cover
            $coverArray[] = array(
                'policyOptionID'    => $optionIDsArray['contentstp'],
                'sumInsured'        => is_null($amountsCovered[self::CONTENTS])?0:$amountsCovered[self::CONTENTS],
                'premium'           => is_null($optionPremiums[self::CONTENTS])?0:$optionPremiums[self::CONTENTS],
                'policyNumber'      => $this->_quoteModel->policyNumber);

            $coverArray[] = array(
                'policyOptionID'    => $optionIDsArray['pedalcyclesp'],
                'sumInsured'        => is_null($amountsCovered[self::PEDALCYCLES])?0:$amountsCovered[self::PEDALCYCLES],
                'premium'           => is_null($optionPremiums[self::PEDALCYCLES])?0:$optionPremiums[self::PEDALCYCLES],
                'policyNumber'      => $this->_quoteModel->policyNumber);

            $coverArray[] = array(
                'policyOptionID'    => $optionIDsArray['specpossessionsp'],
                'sumInsured'        => is_null($amountsCovered[self::SPECIFIEDPOSSESSIONS])?0:$amountsCovered[self::SPECIFIEDPOSSESSIONS],
                'premium'           => is_null($optionPremiums[self::SPECIFIEDPOSSESSIONS])?0:$optionPremiums[self::SPECIFIEDPOSSESSIONS],
                'policyNumber'      => $this->_quoteModel->policyNumber);

            $coverArray[] = array(
                'policyOptionID'    => $optionIDsArray['possessionsp'],
                'sumInsured'        => is_null($amountsCovered[self::UNSPECIFIEDPOSSESSIONS])?0:$amountsCovered[self::UNSPECIFIEDPOSSESSIONS],
                'premium'           => is_null($optionPremiums[self::UNSPECIFIEDPOSSESSIONS])?0:$optionPremiums[self::UNSPECIFIEDPOSSESSIONS],
                'policyNumber'      => $this->_quoteModel->policyNumber);
            
            $policyCover->setCover($this->_quoteModel->policyNumber, $coverArray);
        }
        $this->_save();
    }

    /**
     * Set cover amount
     *
     * @param int $sumInsured
     * @return void
     */
    public function getCoverAmount($type) {
        // Get the current amounts covered
        $amountsCovered = explode('|', $this->_quoteModel->amountsCovered);

        $sumInsured = $amountsCovered[$type];

        return $sumInsured;
    }

    /**
     * Get the agent scheme number
     *
     * @return int Agent scheme number
     **/
    public function getAgentSchemeNumber() {
        return $this->_quoteModel->agentSchemeNumber;
    }

    /**
     * Set the agent scheme number
     *
     * @param int $asn Agent scheme number
     *
     * @return void
     **/
    public function setAgentSchemeNumber($asn) {
        $this->_quoteModel->agentSchemeNumber = $asn;
        $agent = new Datasource_Core_Agents();
        $this->_quoteModel->agentRateSetID = $agent->getRatesetID($this->_quoteModel->agentSchemeNumber);

        // Premiums are dependant on the agent scheme number so we need to recalculate premiums before saving
        $this->calculatePremiums();
        $this->_save();
    }

    /**
    * Calculate the Premium
    *
    */
    public function calculatePremiums()
    {
        Application_Core_Logger::log('Calculating Premiums','info');

        // Split the sum insured amounts
        $amountsCovered = explode('|', $this->_quoteModel->amountsCovered);
        $contentsSumInsured = $amountsCovered[0];
        $pedalCyclesSumInsured = $amountsCovered[1];
        $specifiedPossessionsSumInsured = $amountsCovered[2];
        $unspecifiedPossessionsSumInsured = $amountsCovered[3];

        // Get IPT percentage unless we are doing just a quick quote
        if (isset($this->_quoteModel->ID)) {
			$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
			$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
			$postcode = $properties[0]['postcode'];
			
			$taxDatasource = new Datasource_Core_Tax();
			$tax = $taxDatasource->getTaxbyTypeAndPostcode('ipt', $postcode);
			$ipt = 1 + ($tax['rate']/100);
		} else {
			// We're just doing a quick quote so just get the regular ipt
			$taxDatasource = new Datasource_Core_Tax();
			$tax = $taxDatasource->getTaxbyType('ipt');
			$ipt = 1 + ($tax['rate']/100);
		}
		
        // Zero the premium amounts
        $premiums = new Model_Insurance_TenantsContentsPlus_Premiums();

        // TODO: This rating engine is flawed and produces un-rounded values.
        //       Need to replace this with the landlords CORE rating engine which is currently unfinished.

        // Load the rates
        $ratesManager = new Manager_Insurance_TenantsContentsPlus_Rates($this->_quoteModel->agentRateSetID, $this->_quoteModel->riskArea,$this->_rateSetID);
        $ratesManager->setCoverAmounts($contentsSumInsured, $unspecifiedPossessionsSumInsured);

        // Fetch the rate set ID and store it in the object
        $this->_quoteModel->rateSetID = $ratesManager->getSetID();

        // Now we have the rate set ID - let's get the rates
        $contentsRate = $ratesManager->getContentsRate();
        $pedalCyclesRate = $ratesManager->getPedalCyclesRate();
        $specifiedPossessionsRate = $ratesManager->getSpecifiedPossessionsRate();
        $unspecifiedPossessionsRate = $ratesManager->getUnspecifiedPossessionsRate();

        // Calculate premiums
        if ($contentsSumInsured>0) {
            if ($contentsSumInsured > 15000) {
                $premiums->contents = ((($contentsRate * $ipt) * ($contentsSumInsured/1000)));
            } else {
                $premiums->contents = ($contentsRate * $ipt);
            }
        }

        if ($pedalCyclesSumInsured >= 200) {
            $premiums->pedalCycles = round((($pedalCyclesRate * $pedalCyclesSumInsured) / 100) * $ipt, 3);
        } else {
            $premiums->pedalCycles = 0;
        }

        if ($specifiedPossessionsSumInsured > 0) {
            $premiums->specifiedPossessions = round((($specifiedPossessionsSumInsured / 100) * $specifiedPossessionsRate) * $ipt, 3);
        }

        if ($unspecifiedPossessionsSumInsured > 0) {
            $premiums->unspecifiedPossessions = $unspecifiedPossessionsRate * $ipt;
        }
        
        // Calculate totals with Loadings
        $premiums->annualTotal = (($premiums->contents + $premiums->pedalCycles + $premiums->specifiedPossessions + $premiums->unspecifiedPossessions)* $this->_quoteModel->discountLoading) * 12;
        $premiums->annualIptAmount = $premiums->annualTotal - ($premiums->annualTotal / $ipt);
        $premiums->total = ($premiums->contents + $premiums->pedalCycles + $premiums->specifiedPossessions + $premiums->unspecifiedPossessions) * $this->_quoteModel->discountLoading;
        $premiums->iptAmount = $premiums->total - ($premiums->total / $ipt);

        // Apply loadings to individual breakdowns
        $premiums->contents = $premiums->contents * $this->_quoteModel->discountLoading;
        $premiums->pedalCycles = $premiums->pedalCycles * $this->_quoteModel->discountLoading;
        $premiums->specifiedPossessions = $premiums->specifiedPossessions * $this->_quoteModel->discountLoading;
        $premiums->unspecifiedPossessions = $premiums->unspecifiedPossessions * $this->_quoteModel->discountLoading;

        // Update the optionpremiums field in the database
        // These are rounded to 2 decimal places and do not include IPT
        $optionPremiums = explode('|', $this->_quoteModel->optionPremiums);
        $optionPremiums[0] = round($premiums->contents/$ipt ,2);
        $optionPremiums[1] = round($premiums->pedalCycles/$ipt ,2);
        $optionPremiums[2] = round($premiums->specifiedPossessions/$ipt ,2);
        $optionPremiums[3] = round($premiums->unspecifiedPossessions/$ipt ,2);
        $optionPremiums[4] = 0;
        $optionPremiums = implode('|', $optionPremiums);

        // Calculate annual individual breakdowns
        $premiums->annualContents = ($premiums->contents * $this->_quoteModel->discountLoading) * 12;
        $premiums->annualPedalCycles = ($premiums->pedalCycles * $this->_quoteModel->discountLoading) * 12;
        $premiums->annualSpecifiedPossessions = ($premiums->specifiedPossessions * $this->_quoteModel->discountLoading) * 12;
        $premiums->annualUnspecifiedPossessions = ($premiums->unspecifiedPossessions * $this->_quoteModel->discountLoading) * 12;

        // Now that ALL calculations are done we can safely round to 2 decimal places
        $premiums->annualTotal = round($premiums->annualTotal, 2);
        $premiums->annualIptAmount = round($premiums->annualIptAmount, 2);
        $premiums->total = round($premiums->total, 2);
        $premiums->iptAmount = round($premiums->iptAmount , 2);
        $premiums->contents = round($premiums->contents, 2);
        $premiums->pedalCycles = round($premiums->pedalCycles, 2);
        $premiums->specifiedPossessions = round($premiums->specifiedPossessions, 2);
        $premiums->unspecifiedPossessions = round($premiums->unspecifiedPossessions, 2);
        $premiums->annualContents = round($premiums->annualContents, 2);
        $premiums->annualPedalCycles = round($premiums->annualPedalCycles, 2);
        $premiums->annualSpecifiedPossessions = round($premiums->annualSpecifiedPossessions, 2);
        $premiums->annualUnspecifiedPossessions = round($premiums->annualUnspecifiedPossessions, 2);
		
		if ($this->getPayBy()=="Annually") {
	        $this->_quoteModel->premium = $premiums->annualTotal - $premiums->annualIptAmount;
	        $this->_quoteModel->ipt = $premiums->annualIptAmount;
	        $this->_quoteModel->quote = $premiums->annualTotal;
	    } else {
	    	$this->_quoteModel->premium = $premiums->total - $premiums->iptAmount;
	        $this->_quoteModel->ipt = $premiums->iptAmount;
	        $this->_quoteModel->quote = $premiums->total;
	    }
        $this->_quoteModel->optionPremiums = $optionPremiums;
        $amountsCovered[4]="no";
        $this->_quoteModel->amountsCovered = implode('|',$amountsCovered); 
        $this->_save();
		
        return($premiums);
    }



    /**
    * Get the value of the payBy field
    */
    public function getPayMethod() {
        return $this->_quoteModel->payMethod;
    }

    /**
    * Get the value of the payBy field
    */
    public function getPayBy() {
        return $this->_quoteModel->payBy;
    }

    /**
    * get value of the quote field
    */
    public function getPolicyQuote() {
        return $this->_quoteModel->quote;
    }

    /**
    * get the value policy number
    */
    public function getPolicyNumber() {
        return $this->_quoteModel->policyNumber;
    }

    /**
    * Get the reference number
    */
    public function getRefno() {
        return $this->_quoteModel->refNo;
    }

    /**
    * Get the policy type
    */
    public function getPolicyName() {
        return $this->_quoteModel->policyName;
    }

    public function getStartDate() {
        return $this->_quoteModel->startDate;
    }

    public function getEndDate() {
        return $this->_quoteModel->endDate;
    }

    /*
     * @todo: Add type checks
     */
    public function setStartAndEndDates($startDate, $endDate) {
        $this->_quoteModel->startDate = $startDate;
        $this->_quoteModel->endDate   = $endDate;
        $this->_save();
    }
	
	public function setIssueDate($issueDate) {
        $this->_quoteModel->issueDate = $issueDate;
        $this->_save();
	}

    public function getIsHighRisk() {
        return $this->_quoteModel->isHighRisk;
    }

    /**
     * Getter for the policy amounts covered.
     *
     * Returns the amount by which $optionName is covered on the current
     * quote / policy. This can be used to identify specific cover amounts,
     * without the need of extracting the entire contents of the amountscovered
     * field and splitting up the pipe-delimited fields.
     *
     * @param string $optionName
     * The policy option for which the amount covered will be returned.
     *
     * @return mixed
     * The amount covered for the $optionName specified on the current quote / policy,
     * encapsulated in a Zend_Currency object. Returns null if no amount can be found.
     */
    public function getPolicyOptionAmountCovered($optionName) {

        //Put the policyoptions and amountscovered into arrays for easier
        //processing.
        $policyOptionsArray = explode("|", $this->_quoteModel->policyOptions);
        $amountsCoveredArray = explode("|", $this->_quoteModel->amountsCovered);


        //Now determine if the $optionName exists in the policyoptions, and
        //if yes, return its corresponding amountscovered value.
        if(in_array($optionName, $policyOptionsArray)) {
           $key = array_search($optionName, $policyOptionsArray);
           $returnVal = $amountsCoveredArray[$key];
        }

        //Compose the return value consistent with this function's contract.
        if(empty($returnVal)) {
            $returnVal = null;
        }
        else {

            $returnVal = new Zend_Currency(
                array(
                    'value' => $returnVal,
                    'precision' => 0
                )
            );
        }
        return $returnVal;
    }

    /**
     * Removes a quote from the datasource.
     *
     * @param array $args
     * An array of identifiers which can be used to identify the quote
     * to be deleted. The identifiers MUST correspond to one of the quote
     * identifier consts exposed by this class: CUSTOMER_REFNO or
     * POLICY_NUMBER. Only one is required.
     *
     * @return void
     */
    public static function remove($args) {

        $quoteDatasource = new Datasource_Insurance_Quotes();
        $quoteDatasource->remove($args);
    }

    /**
     * Sets the policy term in the policyTerm table.
     *
     * @return void
     */
    public function setPolicyTerm() {
        $policyTermDatasource = new Datasource_Insurance_Policy_Term();
        $policyTermDatasource->insertPolicyTerm($this->_quoteModel);
    }

    /**
     * Description given in the IChangeable interface.
     */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
	//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
	    $policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
	}

        //Create a policy object from the data passed in.
        $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $quote = $quoteDatasource->getByPolicyNumber($quoteNumber);
        $quote->policyNumber = $policyNumber;
        $quote->status = "Policy";

        //Set the issueDate and timecompleted fields (which oddly record the same value but
        //in different formats).
        $issueDate = Zend_Date::now();
        $quote->issueDate = $issueDate->toString(Zend_Date::ISO_8601);
        $quote->timeCompleted = $issueDate->toString(Zend_Date::TIMESTAMP);

        // Update the optionpremiums field in the database
        if($quote->payBy=="Annually") {
	        $optionPremiums = explode('|',$quote->optionPremiums);
	        $optionPremiums[0] = round($optionPremiums[0]*$quote->policyLength,2);
	        $optionPremiums[1] = round($optionPremiums[1]*$quote->policyLength,2);
	        $optionPremiums[2] = round($optionPremiums[2]*$quote->policyLength,2);
	        $optionPremiums[3] = round($optionPremiums[3]*$quote->policyLength,2);
	        $optionPremiums = implode('|', $optionPremiums);
	        $quote->optionPremiums = $optionPremiums;
        }

        //Write the policy to the datasource
        $policyDatasource = new Datasource_Insurance_TenantsContentsPlus_Policies();
        $policyDatasource->save($quote);

        //Finally, delete the quote.
        $quoteDatasource->remove(array(Manager_Insurance_TenantsContentsPlus_Quote::POLICY_NUMBER => $quoteNumber));
    }

    public function getQuoteObject() {
        return $this->_quoteModel;
    }

    public function update($quote) {
        $this->_quoteModel = $quote;
        $this->_save();
    }
    
    public static function sendQuote($policyNumber, $method)
    {
        $params = Zend_Registry::get('params');
        $homeletServer = $params->homelet->get('legacyDomain');
        $remoteHost = $homeletServer;
        $serviceName = $params->homelet->get('sendQuoteService');
        $letterSendingScriptPath = $remoteHost . '/'. $serviceName;
        
        $getString = "policynumber=$policyNumber";
        $getString .= "&";
        $getString .= "quote=quote";
        $getString .= "&";
        $getString .= "autoquote=1";
        $getString .= "&";
        $getString .= "method=$method";
        
        #New code to fix autoquote send failure.
        $getString .= "&";
        $getString .= "lettertype=sendquote";        
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$letterSendingScriptPath?$getString");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        //Return true to indicate success.
        return true;
    }

}
?>
