<?php

/**
 * Datasource for storing and querying additional informations provided by the customer
 * in response to underwriting questions presented during a quote, MTA or renewal process.
 */
class Datasource_Insurance_AdditionalInformation extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';     
	protected $_name = 'additionalinfo';
    protected $_primary = 'policynumber';
    
    
	/**
	 * Inserts a new additional information record into the additionalinfo table.
	 *
	 * Function responsible for adding additional information to a quote or policy,
	 * to supplement the underwriting answers.
	 *
	 * @param Model_Insurance_AdditionalInformation $additionalInformation
	 * A Model_Insurance_AdditionalInformation object containing all the additional
	 * information provided by the user when applying for a policy / policy
	 * renewal etc.
	 *
	 * @return void
	 */
    public function insertAdditionalInformation($additionalInformation) {
		
        $data = array(
            'policynumber' => $additionalInformation->getPolicyNumber(),
            'additionalinfo' => $additionalInformation->getAdditionalInformation()
        );
        
        if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert specified possession in table {$this->_name}", 'error');
        }
    }
	
	
	/**
	 * Retrieves an existing additional information record.
	 *
	 * Attempts to retrieve an existing additional information record associated
	 * with the quote / policy number passed in. Returns the data encapsulated
	 * in an Model_Insurance_AdditionalInformation object.
	 *
     * @param string $policyNumber
     * Search for records matching this quote / policy number.
	 *
	 * @return Model_Insurance_AdditionalInformation
	 * Returns this object populated with relevant information, or null if no
	 * relevant information has been stored.
	 */
	public function getAdditionalInformation($policyNumber) {
		
		$select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $additionalInfo = $this->fetchRow($select);
		
		if(!empty($additionalInfo)) {
			
			$additionalInformation = new Model_Insurance_AdditionalInformation();
			$additionalInformation->setPolicyNumber($policyNumber);
			$additionalInformation->setAdditionalInformation($additionalInfo->additionalinfo);
			$returnVal = $additionalInformation;
		}
		else {
			// No warning given as this is a common/normal scenario
			$returnVal = null;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Removes additional underwriting information from the data storage.
	 *
	 * Method responsible for removing additional information stored against the
	 * $policyNumber passed in.
	 *
	 * @param string $policyNumber
	 * Identifier for the additional underwriting information.
	 *
	 * @return void
	 */
	public function removeAdditionalInformation($policyNumber) {
		
        $where = $this->quoteInto('policynumber = ?', $policyNumber);
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
		
		$where = $this->quoteInto('policynumber = ?', $quoteNumber);
		$updatedData = array('policynumber' => $policyNumber);
		return $this->update($updatedData, $where);		
	}
}

?>