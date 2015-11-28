<?php

/**
 * Encapsulates referencing status business logic.
 * 
 * All access to the status datasources should be through this class.
 */
class Manager_Referencing_Status {

	protected $_referenceStateDatasource;
	protected $_referenceStateReasonMap;
    
    protected function _loadSources() {
    	
    	if(empty($this->_referenceStateDatasource)) {
    		
			$this->_referenceStateDatasource = new Datasource_Referencing_ReferenceState();
		}
		
		if(empty($this->_referenceStateReasonMap)) {
			
			$this->_referenceStateReasonMap = new Datasource_Referencing_ReferenceStateReasonMap();
		}
    }
    
    /**
     * Save a referencing status.
     * 
     * @param Model_Referencing_ReferenceStatus $status
     * The referencing status to save.
     * 
     * @return void
     */
    public function save($status) {
		
		$this->_loadSources();
    	$this->_referenceStateDatasource->setReferenceState($status);
		$this->_referenceStateReasonMap->setReferenceStateReason($status);
    }
    
    /**
     * Retrieve a referencing status.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return Model_Referencing_ReferenceStatus
     * The reference status, or null if this has not yet been set.
     */
    public function retrieve($referenceId) {		
		
	    $this->_loadSources();
    	$status = $this->_referenceStateDatasource->getReferenceState($referenceId);
		if(!empty($status)) {
			
			$status->reasonForState = $this->_referenceStateReasonMap->getReferenceStateReason($referenceId);
		}
		return $status;
    }
}

?>