<?php

/**
 * Business rules class which provides underwriting terms services.
 */
class Manager_Insurance_LandlordsPlus_Terms {
	
	protected $_termsModel;
	
	
	/**
	 * Determines if a flood risk is applicable for a given postcode.
	 * 
	 * @param string $postCode
	 * The postcode to check.
	 * 
	 * @return boolean
	 * True if there is a flood risk, false otherwise.
	 */
    public function isFloodRiskApplicable($postCode) {
		
		if(empty($this->_termsModel)) {
			
			$this->_termsModel = new Datasource_Insurance_LandlordsPlus_Terms();
		}
		
    	$floodScore = $this->_termsModel->getFloodRiskScore($postCode);
		if ($floodScore > 0) {
			
			$returnVal = true;
		}
		else {
			
			$returnVal = false;
		}
		
		return $returnVal;
	}
	
    
	/**
	 * Determines if there is a high flood risk for a given postcode.
	 * 
	 * @param string $postCode
	 * The postcode to check.
	 * 
	 * @return boolean
	 * True if there is a high risk of flood, false otherwise.
	 */
	public function isFloodRiskHigh($postCode) {
		
        $params = Zend_Registry::get('params');
		
		if(empty($this->_termsModel)) {
			
			$this->_termsModel = new Datasource_Insurance_LandlordsPlus_Terms();
		}
        
		$floodScore = $this->_termsModel->getFloodRiskScore($postCode);
		$highRiskFloodScore = $params->uw->et->landlordsp->mandatory->floodExclusion->score;
		
		if ($floodScore == $highRiskFloodScore) {
			
			$returnVal = true;
		}
		else {
			
			$returnVal = false;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Detrmines if a postcode is recognised by the automatic underwriting terms datasource.
	 * 
	 * @param string $postCode
	 * The postcode to check.
	 * 
	 * @return boolean
	 * True if automatic underwriting terms exist for the postcode, false otherwise.
	 */
	public function getPostcodeHasTerms($postCode) {
		
		if(empty($this->_termsModel)) {
			
			$this->_termsModel = new Datasource_Insurance_LandlordsPlus_Terms();
		}
		return $this->_termsModel->getPostcodeHasTerms($postCode);
	}
}

?>