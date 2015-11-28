<?php

/**
 * Model definition for the interest table. 
 */
class Datasource_Insurance_LegacyBankInterest extends Zend_Db_Table_Multidb {

	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'interest';
    protected $_primary = 'interestID';

    
    /**
	 * Updates an existing bank interest in the interest table.
	 *
	 * @param Model_Insurance_LegacyBankInterest $bankInterest
	 * A Model_Insurance_LegacyBankInterest object containing all the bank interest information.
	 *
	 * @return boolean
	 * Return true if the bank interest is updated, false otherwise. 
	 */
    public function updateInterest($bankInterest) {
    	
    	//Ignore rubbish.
    	$interestId = $bankInterest->getInterestId();
    	if(empty($interestId)) {
    		
    		Application_Core_Logger::log("Can't update bank interest in table {$this->_name}", 'error');
    		return false;
    	}
    	
		$data = array(
			'interestID' => $bankInterest->getInterestId(),
            'refno' => $bankInterest->getRefno(),
            'policynumber' => $bankInterest->getPolicyNumber(),
            'bankname' => $bankInterest->getBankName(),
            'bankaddress1' => $bankAddress->addressLine1,
            'bankaddress2' => $bankAddress->addressLine2,
            'bankaddress3' => $bankAddress->town,
        	'bankaddress4' => $bankInterest->county,
        	'bankpostcode' => $bankAddress->postCode,
        	'accountnumber' => $bankInterest->getAccountNumber()
        );
		
		$where = $this->quoteInto('interestId = ?', $bankInterest->getInterestId());
		if($this->update($data, $where) > 0) {
			
			$returnVal = true;
		}
		else {
			
			$returnVal = false;
		}
		return $returnVal;
    }
    
    
    /**
	 * Updates all bank interests for a quote/policy.
	 *
	 * @param array $bankInterests
	 * An array of Model_Insurance_LegacyBankInterest objects containing all the bank 
	 * interest informations.
	 *
	 * @return void
	 */
    public function updateAllInterests($bankInterests) {
    	
    	foreach($bankInterests as $bankInterest) {
    		
    		//Ignore rubbish.
    		$interestId = $bankInterest->getInterestId();
    	    if(empty($interestId)) {
    		
    			Application_Core_Logger::log("Can't update bank interest in table {$this->_name}", 'error');
    			continue;
    		}
    		
    		$data = array(
				'interestID' => $bankInterest->getInterestId(),
	            'refno' => $bankInterest->getRefno(),
	            'policynumber' => $bankInterest->getPolicyNumber(),
	            'bankname' => $bankInterest->getBankName(),
	            'bankaddress1' => $bankAddress->addressLine1,
	            'bankaddress2' => $bankAddress->addressLine2,
	            'bankaddress3' => $bankAddress->town,
	        	'bankaddress4' => $bankInterest->county,
	        	'bankpostcode' => $bankAddress->postCode,
	        	'accountnumber' => $bankInterest->getAccountNumber()
	        );
			
			$where = $this->quoteInto('interestId = ?', $bankInterest->getInterestId());
			$this->update($data, $where);
    	}
    }
    
    
    /**
	 * Inserts a new bank interest into the interest table.
	 *
	 * @param Model_Insurance_LegacyBankInterest $bankInterest
	 * A Model_Insurance_LegacyBankInterest object containing all the bank interest information.
	 *
	 * @return boolean
	 * Returns true if the bank interest was successfully inserted, false otherwise.
	 */
    public function insertInterest($bankInterest) {
		
        $bankAddress = $bankInterest->getBankAddress();
    	$data = array(
            'refno' => $bankInterest->getRefno(),
            'policynumber' => $bankInterest->getPolicyNumber(),
            'bankname' => $bankInterest->getBankName(),
            'bankaddress1' => $bankAddress->addressLine1,
            'bankaddress2' => $bankAddress->addressLine2,
            'bankaddress3' => $bankAddress->town,
        	'bankaddress4' => $bankAddress->county,
        	'bankpostcode' => $bankAddress->postCode,
        	'accountnumber' => $bankInterest->getAccountNumber()
        );
        
        if($this->insert($data)) {
			
			$returnVal = true;
		}
		else {
		
	        //Failed insertion
	        $returnVal = false;
	        Application_Core_Logger::log("Can't insert bank interest in table {$this->_name}", 'error');
		}
		
		return $returnVal;
    }
    
    
    /**
	 * Inserts a batch of new bank interests into the interest table.
	 *
	 * @param array $bankInterest
	 * An array of Model_Insurance_LegacyBankInterest objects containing all the bank 
	 * interest informations.
	 *
	 * @return void
	 */
    public function insertAllInterests($bankInterests) {
    	
    	foreach($bankInterests as $bankInterest) {
    		
	    	$bankAddress = $bankInterest->getBankAddress();
	    	$data = array(
	            'refno' => $bankInterest->getRefno(),
	            'policynumber' => $bankInterest->getPolicyNumber(),
	            'bankname' => $bankInterest->getBankName(),
	            'bankaddress1' => $bankAddress->addressLine1,
	            'bankaddress2' => $bankAddress->addressLine2,
	            'bankaddress3' => $bankAddress->town,
	        	'bankaddress4' => $bankAddress->county,
	        	'bankpostcode' => $bankAddress->postCode,
	        	'accountnumber' => $bankInterest->getAccountNumber()
	        );
	        
	        if(!$this->insert($data)) {

		        //Failed insertion
		        Application_Core_Logger::log("Can't insert bank interest in table {$this->_name}", 'error');
			}
    	}
    }
    
    
    /**
	 * Returns a bank interest identified by the $interestId passed in.
	 *
	 * @param integer $interestId
	 * The unique bank interest identifier.
	 * 
	 * @return mixed
	 * Returns a Model_Insurance_LegacyBankInterest object corresponding to the $interestId
	 * passed in, or null if none found.
	 */
    public function getInterest($interestId) {
    	
    	$select = $this->select();
        $select->where('interestID = ?', $interestId);		
        $result = $this->fetchRow($select);
		
		if(empty($result)) {
			
			$returnVal = false;
		}
		else {
			
			$bankInterest = new Model_Insurance_LegacyBankInterest();
			$bankInterest->setInterestId($interestId);
			$bankInterest->setRefno($result->refno);
			$bankInterest->setPolicyNumber($result->policynumber);
			$bankInterest->setBankName($result->bankname);
			
			$address = new Model_Core_Address();
			$address->addressLine1 = $result->bankaddress1;
			$address->addressLine2 = $result->bankaddress2;
			$address->town = $result->bankaddress3;
			$address->county = $result->bankaddress4;
			$address->postCode = $result->bankpostcode;
			$bankInterest->setBankAddress($address);
			
			$bankInterest->setAccountNumber($result->accountnumber);
			$returnVal = $bankInterest;
		}
		
		return $returnVal;
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
     * Returns an array of Model_Insurance_LegacyBankInterest objects, or null if no bank interest
     * found.
     */
    public function getAllInterests($policyNumber = null, $refNo = null) {

    	//Ignore rubbish.
    	if(empty($policyNumber) && empty($refNo)) {
    		
    		Application_Core_Logger::log("Can't insert bank interest in table {$this->_name}", 'error');
    		return null;
    	}
    	
    	
    	//Retrieve the bank interests.
    	$select = $this->select();
    	if(!empty($policyNumber)) {
    	
    		$select->where('policynumber = ?', $policyNumber);
    	}
    	else {
    		
    		$select->where('refno = ?', $refNo);
    	}
        $rows = $this->fetchAll($select);
		
        
        //Insert the interests into BankInterest objects.
        $returnArray = array();
		if(count($rows) > 0) {
			
			foreach($rows as $currentRow) {
				
				$bankInterest = new Model_Insurance_LegacyBankInterest();
				
				$bankInterest->setInterestId($currentRow->interestID);
				$bankInterest->setRefno($currentRow->refno);
				$bankInterest->setPolicyNumber($currentRow->policynumber);
				$bankInterest->setBankName($currentRow->bankname);
				
				$address = new Model_Core_Address();
				$address->addressLine1 = $currentRow->bankaddress1;
				$address->addressLine2 = $currentRow->bankaddress2;
				$address->town = $currentRow->bankaddress3;
				$address->county = $currentRow->bankaddress4;
				$address->postCode = $currentRow->bankpostcode;
				$bankInterest->setBankAddress($address);
				
				$bankInterest->setAccountNumber($currentRow->accountnumber);
				$returnArray[] = $bankInterest;
			}
		}
		
		
		//Return the bank interests consistent with this function's contract.
		if(empty($returnArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $returnArray;
		}
		return $returnVal;
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
    	
    	$where = $this->quoteInto('interestID = ?', $interestId);
        $this->delete($where);
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
    	
        //Ignore rubbish.
    	if(empty($policyNumber) && empty($refNo)) {
    		
    		Application_Core_Logger::log("Can't insert bank interest in table {$this->_name}", 'error');
    		return null;
    	}
    	
    	if(!empty($policyNumber)) {
    	
    		$where = $this->quoteInto('policynumber = ?', $policyNumber);
    	}
    	else {
    		
    		$where = $this->quoteInto('refno = ?', $refNo);
    	}
    	
        $this->delete($where);
    }
    
	
	/**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {		
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$data = array('policynumber' => $policyNumber);
		$where = $this->quoteInto('policynumber = ?', $quoteNumber);
		return $this->update($data, $where);
	}
}

?>