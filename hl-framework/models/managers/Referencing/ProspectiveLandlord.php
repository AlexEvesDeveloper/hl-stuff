<?php

/**
 * Encapsulates referencing prospective landlord business logic.
 * 
 * All access to the prospective landlord datasources should be through this class.
 */
class Manager_Referencing_ProspectiveLandlord {	
	
	protected $_landlordDatasource;
	protected $_landlordMapDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_landlordDatasource)) {
    		
			$this->_landlordDatasource = new Datasource_Referencing_ProspectiveLandlord();
		}
		
		if(empty($this->_landlordMapDatasource)) {
			
			$this->_landlordMapDatasource = new Datasource_Referencing_ProspectiveLandlordMap();
		}
    }
    
    /**
     * Used to determine whether or not to insert a placeholder.
     * 
     * A prospective landlord placeholder MUST be inserted into the datasource before
     * save operations can be performed. Therefore, calling code should use this
     * method to determine if a placeholder needs to be created.
     * 
     * @return boolean
     * Returns true if a placeholder has already been created, false otherwise.
     */
    public function getPlaceholderExists() {
    	
    	throw new Zend_Exception(__FUNCTION__ . ' : not yet implemented');
    }

    /**
     * Creates a new, empty ProspectiveLandlord and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * Links the new ProspectiveLandlord to the Reference.
     *
     * @return Model_Referencing_ProspectiveLandlord
     * Returns the newly created, empty ProspectiveLandlord.
     */
    public function insertPlaceholder($referenceId) {
		$this->_loadSources();
		$prospectiveLandlord = $this->_landlordDatasource->insertPlaceholder($referenceId);
		$this->_landlordMapDatasource->insertPlaceholder($prospectiveLandlord->id, $referenceId);
		return $prospectiveLandlord;
	}
	
    /**
     * Save a referencing prospective landlord.
     * 
     * @param Model_Referencing_ProspectiveLandlord $prospectiveLandlord
     * The referencing prospective landlord to save.
     * 
     * @return void
     */
    public function save($prospectiveLandlord) {
		
		$this->_loadSources();
    	$this->_landlordDatasource->updateProspectiveLandlord($prospectiveLandlord);
    }
    
    /**
     * Retrieve a referencing property lease.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return Model_Referencing_PropertyLease
     * The reference property lease, or null if this has not yet been set.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
	    $prospectiveLandlordId = $this->_landlordMapDatasource->getProspectiveLandlordId($referenceId);
        
        if(empty($prospectiveLandlordId)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $this->_landlordDatasource->getByProspectiveLandlordId($prospectiveLandlordId);
        }
        
        return $returnVal;
    }
}

?>