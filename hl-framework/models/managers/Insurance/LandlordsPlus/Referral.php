<?php

/**
 * Business rules class which provides underwriting referrals services for LI+ products.
 */
class Manager_Insurance_LandlordsPlus_Referral extends Manager_Insurance_Referral{

	protected $_ignoreReferencingQuestion;
	
	
	/**
	 * Constructor.
	 * 
	 * @param boolean $ignoreReferencingQuestion
	 * Indicates if the answer to the underwriting referencing question should be ignored
	 * in later processing.
	 */
	public function __construct($ignoreReferencingQuestion = false) {
		
		$this->_ignoreReferencingQuestion = $ignoreReferencingQuestion;
	}
	
	
	/**
	 * Implements abstract method from superclass - refer to superclass for description.
	 */
    public function getReferralReasons($quoteId) {

        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteId);
        $policyNumber = $quoteManager->getLegacyID();
		$refNo = $quoteManager->getLegacyCustomerReference();
		
		
    	//Obtain the sum insured from the policyNumber.
		$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE);
		$rentGuaranteeSumInsured = 0;
		if(!empty($productMeta)) {
			
			$rentGuaranteeSumInsured = $productMeta['monthly_rent'];	
		}


    	//Obtain the buildings sum insured from the policyNumber.
		$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
		$buildingsSumInsured = 0;
                $buildingsType = '';
		if(!empty($productMeta)) {
		
			$buildingsSumInsured = $productMeta['rebuild_value'];
			$buildingsType = $productMeta['building_type'];
		}
    	

    	//Obtain the contents sum insured from the policyNumber.
		$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER);
		$contentsSumInsured = 0;
		if(!empty($productMeta)) {
		
			$contentsSumInsured = $productMeta['cover_amount'];	
		}
		

    	//Obtain the tenancy type ID from the policyNumber.
		$productMeta = $quoteManager->getProperties();
    	$tenancyType = $productMeta[0]['tenant_type_id'];
		
    	
    	$params = Zend_Registry::get('params');
    	$referralReasons = array();
    	
    	
    	//Test the rent guarantee sum insured.
    	$sumInsuredLimit = $params->uw->rt->landlordsp->rentguarantee->maxRent;
		if($rentGuaranteeSumInsured > $sumInsuredLimit) {
			
			$referralReasons[] = $params->uw->rr->landlordsp->rentguarantee->rent;
		}
		
		
		//Test the buildings sum insured to ensure the figure is not too great.
		$sumInsuredLimit = $params->uw->rt->landlordsp->buildings->maxValue;
		if($buildingsSumInsured > $sumInsuredLimit) {
			
			$referralReasons[] = $params->uw->rr->landlordsp->buildings->value;
		}
		
		
		//Test the contents sum insureed to ensure the figure is not too great.
		$sumInsuredLimit = $params->uw->rt->landlordsp->contents->maxValue;
		if($contentsSumInsured > $sumInsuredLimit) {
			
			$referralReasons[] = $params->uw->rr->landlordsp->contents->value;
		}
		
		
		//Test the tenancy type.
		$tenancyTypeAutoRefers = $params->uw->rt->landlordsp->tenancy->type;
		if($tenancyType == $tenancyTypeAutoRefers) {
			
			$referralReasons[] = $params->uw->rr->landlordsp->tenancy->type;
		}
		
		// Test the building type.
                if("Other" == $buildingsType) {

                        $referralReasons[] = $params->uw->rr->landlordsp->buildings->type;
                }
		
		//Test the previous claims.
		$previousClaimsResults = $this->_checkPreviousClaims($quoteId);
		if(!empty($previousClaimsResults)) {
			
			$referralReasons = array_merge($referralReasons, $previousClaimsResults);
		}
		
		
		//Test the underwriting answers.
    	$answersResults = $this->_checkAnswers($quoteId);
		if(!empty($answersResults)) {
			
			$referralReasons = array_merge($referralReasons, $answersResults);
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
	 * Checks the underwriting answers.
     */
    protected function _checkAnswers($quoteId) {
    	
    	$params = Zend_Registry::get('params');
				
		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteId);
        $policyNumber = $quoteManager->getLegacyID();
		$property = $quoteManager->getProperties();
		$postCode = $property[0]['postcode'];
		
		
		$referralReasons = array();
    	
    	//Test 3: Answering the underwriting questions.
		$answersManager = new Manager_Insurance_Answers();
		$answersArray = $answersManager->getUnderwritingAnswers($policyNumber);
		
		if(empty($answersArray)) {

			//You can't process for referral if no underwriting answers have first been provided.
			throw new Zend_Exception(get_class() . __FUNCTION__ . ": no underwriting answers provided.");
		}		
		
		for($i = 0; $i < count($answersArray); $i++) {

			$answerGiven = $answersArray[$i]->getAnswer();
			$expectedAnswer = $answersArray[$i]->getExpectedAnswer();
			$questionNumber = $answersArray[$i]->getQuestionNumber();
			
			//Process questions 53, 60, 61 specially.
			if($questionNumber == '53') {
				
				continue;
			}

			//Question 6 is dealt with specially.
			if($questionNumber == '60') {
				
				if($answerGiven == Model_Insurance_Answer::YES) {

					//Check the extra args.
					$underwritingTerms = new Datasource_Insurance_LandlordsPlus_Terms();
					$subsidenceScore = $underwritingTerms->getSubsidenceRiskScore($postCode);
					if($subsidenceScore == 0) {

						$referralReasons[] = $params->uw->rr->landlordsp->answer;
					}
				}
				continue;
			}

			if($questionNumber == '61') {
				
				//Question 7 is the previous claims answer. The outcome of this is determiend by the
				//previous claims logic in the checkUwReferralState() method.
				continue;
			}

			//This is the referencing question. Some calls to this method may want this answer to
			//be ignored.
			if(($questionNumber == '64') && ($this->_ignoreReferencingQuestion)) {
				
				continue;
			}
			
			
			//All other questions should be processed here.
			if(($expectedAnswer == Model_Insurance_Answer::YES) || ($expectedAnswer == Model_Insurance_Answer::NO)) {
				
				if($answerGiven != $expectedAnswer) {
print("HERE2 {$questionNumber}<BR>");
					$referralReasons[] = $params->uw->rr->landlordsp->answer;
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
    protected function _checkPreviousClaims($quoteId) {
    	
    	$params = Zend_Registry::get('params');
		
		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteId);
		$refNo = $quoteManager->getLegacyCustomerReference();
		

		//Identify if buildings/contents cover is applicable for this policy, and if yes then
		//retrieve the excess values. 
		$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
		if(!empty($productMeta)) {
    	
			$isBuildingsCoverApplicable = true;
			$buildingsExcess = new Zend_Currency(
				array(
					'value' => $productMeta['excess'],
					'precision' => 2
				)
			);
		}
		else {
			
			$isBuildingsCoverApplicable = false;
		}
		
		$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER);
		if(!empty($productMeta)) {
	
		    $isContentsCoverApplicable = true;
			$contentsExcess = new Zend_Currency(
				array(
					'value' => $productMeta['excess'],
					'precision' => 2
				)
			);
		}
		else {

			$isContentsCoverApplicable = false;
		}
    	
    	
    	$previousClaimsReferralReasons = array();
    	
	    
	    //Retrieve the previous claims details, if any.
    	if(empty($this->_previousClaimsModel)) {
	    	
	    	$this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
		}
		
		$previousClaimsArray = $this->_previousClaimsModel->getPreviousClaims($refNo);
		if(empty($previousClaimsArray)) {
			
			return null;
		}
		
		
	    //First test to see if the total value of previous claims exceed the threshold.
	    $claimsTotal = new Zend_Currency(
	        array(
	            'value' => 0,
	            'precision' => 2));
	
	    foreach($previousClaimsArray as $previousClaim) {
	
	        $claimsTotal->add($previousClaim->getClaimValue());
	    }
	
	    $claimsThreshold = new Zend_Currency(
	        array(
	            'value' => $params->uw->rt->landlordsp->claimsThreshold,
	            'precision' => 2
	        ));

	    if($claimsTotal->isMore($claimsThreshold)) {
	
	        $previousClaimsReferralReasons[] = $params->uw->rr->landlordsp->claimsThreshold;
	    }
	    
	    
	    //Next text for multiple claims of the same type.
	    $claimTypeIds = array();
	    $matchFound = false;
		foreach($previousClaimsArray as $previousClaim) {
			
			if(empty($claimTypeIds)) {
				
				$claimTypeIds[] = $previousClaim->getClaimType()->getClaimTypeID();
				continue;
			}
			
			//Compare the current ID against the other ids.
			foreach($claimTypeIds as $currentId) {
				
				if($currentId == $previousClaim->getClaimType()->getClaimTypeID()) {
					
					$matchFound = true;
					break;
				}
			}
			
			if($matchFound) {
				
				//More than one type of claim of the same loss, so this will need referral.
				$previousClaimsReferralReasons[] = $params->uw->rr->landlordsp->multipleSameTypeClaim;
				break;
			}
		}
		
		
		//Next test for more than one claim, and £0 or £100 excess requested.		
		$excessAmountsWhichRefer = $params->uw->rt->landlordsp->excessAmounts->toArray();
		if(count($previousClaimsArray) > 1) {
			
			if($isBuildingsCoverApplicable) {

				foreach($excessAmountsWhichRefer as $excessAmount) {
				
					if($buildingsExcess->compare($excessAmount) == 0) {
						
						$previousClaimsReferralReasons[] = $params->uw->rr->landlordsp->buildings->excessReduction;
						break;
					}	
				}
			}

			if($isContentsCoverApplicable) {

				foreach($excessAmountsWhichRefer as $excessAmount) {
					
					if($contentsExcess->compare($excessAmount) == 0) {
						
						$previousClaimsReferralReasons[] = $params->uw->rr->landlordsp->contents->excessReduction;
						break;
					}
				}
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
     */
    public function setToRefer($quoteId) {

        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteId);
        $quoteManager->setStatus('Referred');
		$quoteManager->save();
    }
}

?>
