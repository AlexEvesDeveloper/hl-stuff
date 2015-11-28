<?php

/**
 * Encapsulates the reference bank account business logic.
 * 
 * All access to the reference bank account datasources should be through this class.
 */
class Manager_Referencing_BankAccount {	

	protected $_bankAccountDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_bankAccountDatasource)) {
    		
			$this->_bankAccountDatasource = new Datasource_Referencing_BankAccount();
		}
    }
    
    /**
     * Used to determine whether or not to insert a placeholder.
     * 
     * A bank account MUST be inserted into the datasource before
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
	 * Creates a new, empty BankAccount in the datasource and returns an object representation of this.
	 *
	 * @param integer $referenceId
	 * The unique Reference identifier. This will allow the new BankAccount to be
	 * linked to the relevant Reference.
	 * 
	 * @return Model_Referencing_BankAccount
	 * The new, empty BankAccount.
	 */
	public function insertPlaceholder($referenceId) {
		$this->_loadSources();
		return $this->_bankAccountDatasource->insertPlaceholder($referenceId);
	}
	
    /**
     * Updates an existing BankAccount.
     *
     * @param Model_Referencing_BankAccount
     * The BankAccount details to update in the datasource.
     *
     * @return void
     */
    public function save($bankAccount) {
		
		$this->_loadSources();
    	$this->_bankAccountDatasource->updateBankAccount($bankAccount);
    }
    
    /**
     * Retrieves the specified BankAccount.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The BankAccount details, encapsulated in a Model_Referencing_BankAccount
     * object, or null if the BankAccount cannot be found.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
	    return $this->_bankAccountDatasource->getByReferenceId($referenceId);
    }

    public function deleteBankAccount($referenceId) {

            $this->_loadSources();
            return $this->_bankAccountDatasource->deleteBankAccount($referenceId);
    }

}

?>
