<?php

/**
 * Encapsulates the reference subject business logic.
 * 
 * All access to the reference subject datasources should be through this class.
 */
class Manager_Referencing_ReferenceSubject {	

	protected $_referenceSubjectDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_referenceSubjectDatasource)) {
    		
			$this->_referenceSubjectDatasource = new Datasource_Referencing_ReferenceSubject();
		}
    }
    
    /**
     * Used to determine whether or not to insert a placeholder.
     * 
     * A reference subject MUST be inserted into the datasource before
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
     * Creates a new reference subject and corresponding record in the datasource.
     *
     * @param integer $reference
     * The unique reference identifier.
     *
     * @return Model_Referencing_ReferenceSubject
     * Returns the newly created reference subject.
     */
    public function insertPlaceholder($referenceId) {
		$this->_loadSources();
		return $this->_referenceSubjectDatasource->insertPlaceholder($referenceId);
	}
	
    /**
     * Save a ReferenceSubject.
     * 
     * @param Model_Referencing_ReferenceSubject $referenceSubject
     * Encapsulates the reference subject details.
     * 
     * @return void
     */
    public function save($referenceSubject) {
		
		$this->_loadSources();
    	$this->_referenceSubjectDatasource->updateReferenceSubject($referenceSubject);
    }
    
    /**
     * Retrieve a reference subject.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return Model_Referencing_ReferenceSubject
     * The reference subject, or null if not set.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
	    return $this->_referenceSubjectDatasource->getByReferenceId($referenceId);
    }
}

?>