<?php

/**
 * Encapsulates referencing property lease business logic.
 * 
 * All access to the property lease datasources should be through this class.
 */
class Manager_Referencing_PropertyLease {	
	
	protected $_propertyLeaseDatasource;
	protected $_propertyLeaseMap;
    
    protected function _loadSources() {
    	
    	if(empty($this->_propertyLeaseDatasource)) {
    		
			$this->_propertyLeaseDatasource = new Datasource_Referencing_PropertyLease();
		}
		
		if(empty($this->_propertyLeaseMap)) {
			
			$this->_propertyLeaseMap = new Datasource_Referencing_PropertyLeaseMap();
		}
    }
    
    /**
     * Used to determine whether or not to insert a placeholder.
     * 
     * A property lease placeholder MUST be inserted into the datasource before
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
     * Inserts a new, empty PropertyLease into the datasource and returns a corresponding object.
     * 
     * This MUST be called to create new PropertyLease objects.
     *
     * @param integer $referenceId
     * Links the new PropertyLease to the Reference.
     *
	 * @return Model_Referencing_PropertyLease
	 * Encapsulates the details of the newly inserted PropertyLease.
     */
    public function insertPlaceholder($referenceId) {

    	$this->_loadSources();
		
    	$propertyLease = $this->_propertyLeaseDatasource->insertPlaceholder($referenceId);
		$this->_propertyLeaseMap->insertPlaceholder($propertyLease->id, $referenceId);
		
		return $propertyLease;
	}
	
    /**
     * Save a referencing property lease.
     * 
     * @param Model_Referencing_PropertyLease $propertyLease
     * The referencing property lease to save.
     * 
     * @return void
     */
    public function save($propertyLease) {
		
		$this->_loadSources();
    	$this->_propertyLeaseDatasource->updatePropertyLease($propertyLease);
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
	    $propertyLeaseId = $this->_propertyLeaseMap->getPropertyLeaseId($referenceId);
        
        if(empty($propertyLeaseId)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $this->_propertyLeaseDatasource->getByPropertyLeaseId($propertyLeaseId);
        }
        
        return $returnVal;
    }
}

?>