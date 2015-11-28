<?php

/**
 * Model definition for the policyEndorsements table. 
 */
class Datasource_Insurance_Endorsements extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'policyEndorsements';
    protected $_primary = array('policynumber', 'endID');
    
    
	/**
	 * Inserts a new endorsement into the policyEndorsements table.
	 *
	 * Function responsible for applying an endorsement to a quote or policy,
	 * by registering the endorsement in the policyEndorsements table.
	 *
	 * @param Model_Insurance_Endorsement $endorsement
	 * A Model_Insurance_Endorsement object containing all the endorsement information.
	 *
	 * @return boolean
	 * Returns true if the endorsement was successfully inserted, false otherwise.
	 */
    public function insertEndorsement($endorsement) {
		
		//Get database-vendor-independent date strings from the Zend_Date objects
		//stored in the Model_Insurance_Endorsement.
		$dateOn = $endorsement->getDateOn()->toString(Zend_Date::ISO_8601);

		//Check for null dates on this one.
		$dateOff = $endorsement->getDateOff();
		if($dateOff instanceof Zend_Date) {
			$dateOff = $dateOff->toString(Zend_Date::ISO_8601);
		}
		
		$effectiveDate = $endorsement->getEffectiveDate()->toString(Zend_Date::ISO_8601);

		
        //Put it all together and insert.
        $endorsementType = $endorsement->getEndorsementType();
        $endorsementId = $endorsementType->getID();
        
        $data = array(
            'policynumber' => $endorsement->getPolicyNumber(),
            'endID' => $endorsementId,
            'excess' => $endorsement->getExcess()->getValue(),
            'dateOn' => $dateOn,
            'dateOff' => $dateOff,
            'effectiveDate' => $effectiveDate
        );
        
        if($this->insert($data)) {
			
			return true;
		}
        // Failed insertion
        Application_Core_Logger::log("Can't insert endorsement in table {$this->_name}", 'error');
		return false;
    }
	
	
	/**
	 * Indicates if a matching endorsement exists in the database.
	 *
	 * Attempts to retrieve a specific endorsement recorded against the quote or
	 * policy specified in the Model_Insurance_Endorsement object passed in.
	 * Returns true or false to indicate this.
	 *
     * @param Model_Insurance_Endorsement $endorsement
     * A Model_Insurance_Endorsement object containing all the endorsement information.
	 *
	 * @return boolean
	 * Returns true if an endorsement has been applied, false otherwise.
	 */
	public function getEndorsement($endorsement) {
		
		$select = $this->select();
        $select->where('policynumber = ?', $endorsement->getPolicyNumber());
        
        $endorsementType = $endorsement->getEndorsementType();
        $endorsementId = $endorsementType->getID();
		$select->where('endID = ?', $endorsementId);
		
        $result = $this->fetchAll($select);
		
		if($result->count() == 0) {
			// No warning given as this is a common/normal scenario
			return false;
		}
		return true;
	}
	
	
	/**
	 * Removes endorsements.
	 *
	 * This method removes all endorsements associated with the $policyNumber
	 * passed in.
	 *
	 * @param string $policyNumber
	 * The quote / policy number used to identify the endorsments to delete,
	 * if any.
	 *
	 * @return void
	 */
	public function removeAllEndorsements($policyNumber) {
		
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