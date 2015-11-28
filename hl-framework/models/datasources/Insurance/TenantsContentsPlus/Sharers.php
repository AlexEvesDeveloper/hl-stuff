<?php

/**
 * Model definition for the occupation table, which stores TCI+ sharers data. 
 */
class Datasource_Insurance_TenantsContentsPlus_Sharers extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'occupation';
    protected $_primary = 'policynumber';
    
	
	/**
     * Inserts sharers into the database.
     *
     * This method provides a convenient way of inserting sharers into the database.
     *
     * @param Model_Insurance_TenantsContentsPlus_Sharers $sharers
     * The Model_Insurance_TenantsContentsPlus_Sharers object containing all the sharers
     * information to be inserted.
     *
     * @return void
     */
    public function insertSharers($sharer) {
		
        //Put it all together and insert.
        $data = array(
            'policynumber' => $sharer->getPolicyNumber(),
            'sharer1' => $sharer->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01),
            'sharer2' => $sharer->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_02)
        );
        
		if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert sharers in table {$this->_name}", 'error');
        }
    }
	
	
	/**
	 * Removes sharers data.
	 *
	 * This method removes all sharers data corresponding to the $policyNumber
	 * passed in.
	 *
	 * @param string $policyNumber
	 * The identifier for the sharers data to be deleted.
	 */
	public function removeAllSharers($policyNumber) {
		
        $where = $this->quoteInto('policynumber = ?', $policyNumber);
        $this->delete($where);
	}
	
	
    /**
	 * Returns sharers data.
	 *
	 * Method which retrieves sharers data for a quote / policy, if any exists,
	 * encapsulated in a Model_Insurance_TenantsContentsPlus_Sharers object.
	 *
	 * @param string $policyNumber
	 * The full identifier for the sharers data to be retrieved
	 *
	 * @return Model_Insurance_TenantsContentsPlus_Sharer_DomainObjects_Sharers
	 * Returns a Model_Insurance_TenantsContentsPlus_Sharers object encapsulating
	 * the sharers data for the $policyNumber, or null if none found.
	 */
	public function getSharers($policyNumber) {
		
		$select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $result = $this->fetchRow($select);
		
		if(!empty($result)) {
			
			$sharers = new Model_Insurance_TenantsContentsPlus_Sharers();
			$sharers->setPolicyNumber($result->policynumber);
			$sharers->setSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01, $result->sharer1);
			$sharers->setSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_02, $result->sharer2);
			
			$returnVal = $sharers;
		}
		else {
            // No warning given as this is a common/normal scenario
			$returnVal = null;
		}
		
		return $returnVal;
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