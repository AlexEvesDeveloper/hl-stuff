<?php

/**
 * Business rules class which provides bank interest services during the underwriting assessment process.
 */
class Manager_Insurance_LegacyBankInterest {

	protected $_bankInterestModel;
	
	
	/**
	 * Returns a bank interest identified by the $interestId passed in.
	 *
	 * @param integer $interestId
	 * The unique bank interest identifier.
	 * 
	 * @return mixed
	 * Returns a Model_Insurance_BankInterest object corresponding to the $interestId
	 * passed in, or null if none found.
     */
	public function getInterest($interestId) {
	
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		return $this->_bankInterestModel->getInterest($interestId);
	}
	
	
    /**
     * Returns each bank interest associated with the quote/policy or reference number passed in.
     * 
     * Only the policynumber of the refno needs to be provided. However, at least one of these
     * must be provided, otherwise this method will return null.
     * 
     * @param string $policyNumber
     * The quote/policy number.
     * 
     * @param string $refNo
     * The policy reference number.
     * 
     * @return mixed
     * Returns an array of Model_Insurance_BankInterest objects, or null if no bank interest
     * found.
     */
	public function getAllInterests($policyNumber = null, $refNo = null) {
	
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		return $this->_bankInterestModel->getAllInterests($policyNumber, $refNo);
	}
	
	
    /**
	 * Updates an existing bank interest in the interest datasource.
	 *
	 * @param Model_Insurance_BankInterest $bankInterest
	 * A Model_Insurance_BankInterest object containing all the bank interest information.
	 *
	 * @return boolean
	 * Returns true if the bank interest is updated, false otherwise. 
	 */
	public function updateInterest($bankInterest) {

		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		return $this->_bankInterestModel->updateInterest($bankInterest);
	}
	
	
    /**
	 * Inserts a new bank interest into the datasource.
	 *
	 * @param Model_Insurance_BankInterest $bankInterest
	 * A Model_Insurance_BankInterest object containing all the bank interest information.
	 *
	 * @return boolean
	 * Returns true if the bank interest was successfully stored, false otherwise.
	 */
	public function insertInterest($bankInterest) {
	
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		$this->_bankInterestModel->insertInterest($bankInterest);
	}
	
	
    /**
	 * Inserts a batch of new bank interests into the interest datasource.
	 *
	 * @param array $bankInterest
	 * An array of Model_Insurance_BankInterest objects containing all the bank 
	 * interest informations.
	 *
	 * @return void
	 */
	public function insertAllInterests($interests) {
	
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		$this->_bankInterestModel->insertAllInterests($interests);
	}
	
	
	/**
	 * Removes a single bank interest.
	 * 
	 * @param integer $interestId
	 * The unique bank interest identifier.
	 * 
	 * @return void
	 */
	public function removeInterest($interestId) {
		
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		$this->_bankInterestModel->removeInterest($interestId);
	}
	
	
	/**
	 * Removes all bank interests associated with the policynumber/refno passed in.
	 * 
	 * @param string $policyNumber
	 * The unique quote/policy identifier.
	 * 
	 * @param string $refNo
	 * The unique legacy customer identifier.
	 * 
	 * @return void
	 */
	public function removeAllInterests($policyNumber = null, $refNo = null) {
		
		if(empty($this->_bankInterestModel)) {
			
			$this->_bankInterestModel = new Datasource_Insurance_LegacyBankInterest ();
		}
		
		$this->_bankInterestModel->removeAllInterests($policyNumber, $refNo);
	}
}

?>