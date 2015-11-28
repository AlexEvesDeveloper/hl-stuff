<?php

/**
 * Business rules class which provides underwriting referrals services for Portfolio products.
 */
class Manager_Insurance_Portfolio_Referral extends Manager_Insurance_Referral {
	
	
	/**
	 * Implements abstract method from superclass - refer to superclass for description.
	 * 
	 * @todo
	 * Not fully implemented yet.
	 */
    public function getReferralReasons($policyNumber) {
    	
    	//@todo
    	//Obtain the buildings sum insured from the policyNumber.
    	$buildingsSumInsured = '1000000';
    	
    	//@todo
    	//Obtain the contents sum insured from the policyNumber.
    	$contentsSumInsured = '10000';
    	
    	
    	//@todo
    	//Obtain the postcode from the policy number.
    	$postCode = 'LN6 7DL';
    	
    	
    	//@todo
    	//Obtain the customer refno to retrieve the previous claims.
    	$refNo = '12345';
    	
    	
    	
    	$params = Zend_Registry::get('params');
    	$referralReasons = array();
		
		
		//Test the buildings sum insured to ensure the figure is not too great.
		$sumInsuredLimit = $params->uw->rt->portfolio->buildings->maxValue;
		if($buildingsSumInsured > $sumInsuredLimit) {
			
			$referralReasons[] = $params->uw->rr->portfolio->buildings->value;
		}
		
		
		//Test the contents sum insureed to ensure the figure is not too great.
		$sumInsuredLimit = $params->uw->rt->portfolio->contents->maxValue;
		if($contentsSumInsured > $sumInsuredLimit) {
			
			$referralReasons[] = $params->uw->rr->portfolio->contents->value;
		}
		
		
		//Test the previous claims.
		$previousClaimsResults = $this->_checkPreviousClaims($refNo);
		if(!empty($previousClaimsResults)) {
			
			$referralReasons = array_merge($referralReasons, $previousClaimsResults);
		}
		
		
		//Test the underwriting answers.
    	$answersResults = $this->_checkAnswers($policyNumber);
		if(!empty($answersResults)) {
			
			$referralReasons = array_merge($referralReasons, $answersResults);
		}
		
		
		//Test the postcode. If the postcode is in range PO30-PO41, then it must refer.
		if(preg_match("/^PO3[0-9]/i", $postCode) || preg_match("/^PO4[0-1]/i", $postCode)) {
			
			$referralReasons[] = $params->uw->rr->portfolio->postCode->value;
		}
		
		
		//Prepare the return value.
		if(empty($referralReasons)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $referralReasons;
		}
		return $returnVal;
    }
    
    
    /**
     * Identifies if the underwriting questions have been correctly answered.
     * 
     * @param string $policyNumber
     * The unique, full quote/policy number.
     * 
     * @todo
     * Not yet complete.
     */
    protected function _checkAnswers($policyNumber) {
    	
    	$referralReasons = array();    			
		
		$answersManager = new Manager_Insurance_Answers();
		$answersArray = $answersManager->getUnderwritingAnswers($policyNumber);
		
		if(empty($answersArray)) {

			//You can't process for referral if no underwriting answers have first been provided.
			throw new Zend_Exception(get_class() . __FUNCTION__ . ": no underwriting answers provided.");
		}	
		
		for($i = 0; $i < count($answersArray); $i++) {

			$answerGiven = $answersArray[$i]->getAnswer();
			$expectedAnswer = $answersArray[$i]->getExpectedAnswer();
			
			
			//All other questions should be processed here.
			if(($expectedAnswer == Model_Insurance_Answer::YES) || ($expectedAnswer == Model_Insurance_Answer::NO)) {
				
				if($answerGiven != $expectedAnswer) {
					
					$referralReasons[] = $params->uw->rr->portfolio->answer;
				}
			}
		}
		
		
		//Return the results consistent with this method's contract.
		if(empty($referralReasons)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $referralReasons;
		}
		return $returnVal;
    }
    
    
    /**
     * Identifies if the previous claims will force a referral.
     * 
     * @param string $refNo
     * The unique legacy customer reference number.
     * 
     * @return mixed
     * Returns an array of referral reasons. If there are no referral reasons,
     * then will return null.
     */
    protected function _checkPreviousClaims($refNo) {
	    
	    $previousClaimsReferralReasons = array();
    	
    	if(empty($this->_previousClaimsModel)) {
	    	
			$this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
		}
	
		$previousClaimsArray = $this->_previousClaimsModel->getPreviousClaims($refNo);
		if(!empty($previousClaimsArray)) {
	
			//Tenant has one or more claims. Add the totals together to see if they exceed
			//the threshold.
			$claimsTotal = new Zend_Currency(
				array(
					'value' => 0,
					'precision' => 2));
	
			foreach($previousClaimsArray as $previousClaim) {
	
				$claimsTotal->add($previousClaim->getClaimValue());
			}
	
	
			//Test against the previous claims threshold
			$claimsThreshold = new Zend_Currency(
				array(
					'value' => $params->uw->rt->portfolio->claimsThreshold,
					'precision' => 2
				));
	
			if($claimsTotal->isMore($claimsThreshold)) {
	
				$previousClaimsReferralReasons[] = $params->uw->rr->portfolio->previousClaims;
			}
		}
    	

		//Return the results consistent with this method's contract.
		if(empty($previousClaimsReferralReasons)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $previousClaimsReferralReasons;
		}
		return $returnVal;
    }
    
    
    /**
     * Implements abstract method from superclass - refer to superclass for description.
     * 
     * @todo
     * Not yet implemented
     */
    public function setToRefer($policyNumber) {

        throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': not yet implemented.');
    }
}

?>